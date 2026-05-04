<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Booking;
use App\Models\Station;
use App\Models\Seat;
use App\Models\Passenger; // Pastikan Model Passenger ada
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Mail\TicketPaidMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class TicketController extends Controller
{
    /**
     * Menampilkan halaman utama pencarian tiket dan riwayat singkat.
     */
    public function index(Request $request)
    {
        $stations = Station::all();

        // Logika pencarian jadwal
        if ($request->has('origin') || $request->has('destination') || $request->has('date')) {
            $schedules = Schedule::with(['train', 'originStation', 'destinationStation'])
                ->where('origin_station_id', $request->origin)
                ->where('destination_station_id', $request->destination)
                ->whereDate('departure_time', $request->date)
                ->get();
        } else {
            $schedules = collect();
        }

        // Ambil riwayat booking milik user yang sedang login
        $myBookings = Auth::check()
            ? Booking::where('user_id', Auth::id())
                ->with(['schedule.train', 'schedule.originStation', 'schedule.destinationStation'])
                ->latest()
                ->take(5)
                ->get()
            : collect();

        return view('tickets.index', compact('stations', 'schedules', 'myBookings'));
    }

    /**
     * Form input data diri penumpang sebelum pilih kursi.
     */
    public function inputPassengers(Request $request, $scheduleId)
    {
        $schedule = Schedule::with('train')->findOrFail($scheduleId);
        $count = $request->query('passenger_count', 1);

        return view('tickets.passengers', compact('schedule', 'count'));
    }

    /**
     * Memproses data penumpang ke session.
     */
    public function processPassengers(Request $request, $scheduleId)
    {
        $request->validate([
            'passengers' => 'required|array|min:1',
            'passengers.*.name' => 'required|string|max:255',
            'passengers.*.nik' => 'required|string|max:20',
        ]);

        // Simpan data penumpang ke Session agar bisa dipasangkan dengan kursi nanti
        Session::put('booking_passengers', $request->passengers);

        return redirect()->route('tickets.selectSeat', $scheduleId);
    }

    /**
     * Menampilkan peta kursi (Seat Selection).
     */
    public function selectSeats($scheduleId)
    {
        $schedule = Schedule::with('train.carriages.seats')->findOrFail($scheduleId);

        // Ambil semua seat_id yang sudah dipesan untuk jadwal ini
        // Pastikan join ke tabel bookings benar
        $bookedSeatIds = DB::table('booking_passengers')
            ->join('bookings', 'bookings.id', '=', 'booking_passengers.booking_id')
            ->where('bookings.schedule_id', $scheduleId)
            ->whereIn('bookings.status', ['pending', 'success'])
            ->pluck('booking_passengers.seat_id')
            ->toArray();

        // Debugging: Hapus tanda komentar dd di bawah ini untuk cek apakah array ada isinya
        // dd($bookedSeatIds);

        return view('tickets.select-seat', compact('schedule', 'bookedSeatIds'));
    }

    /**
     * Menyimpan data booking ke database (Final Transaction).
     */

    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'seat_ids' => 'required|array',
        ]);

        $passengers = session('booking_passengers');

        if (count($request->seat_ids) !== count($passengers)) {
            return back()->with('error', 'Jumlah kursi tidak sesuai dengan jumlah penumpang.');
        }

        try {
            DB::beginTransaction();

            // UBAH: Gunakan nama variabel $bookingId agar sinkron dengan baris redirect di bawah
            $bookingId = DB::table('bookings')->insertGetId([
                'booking_code' => 'KAI-' . strtoupper(Str::random(10)),
                'user_id' => Auth::id(),
                'schedule_id' => $request->schedule_id,
                'total_price' => count($request->seat_ids) * 30000,
                'status' => 'success',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($request->seat_ids as $index => $seatId) {
                DB::table('booking_passengers')->insert([
                    'booking_id' => $bookingId, // Pastikan variabelnya sama ($bookingId)
                    'seat_id'    => $seatId,
                    'name'       => $passengers[$index]['name'],
                    'nik'        => $passengers[$index]['nik'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();
            session()->forget('booking_passengers');

            // Sekarang $bookingId sudah dikenali dan garis merah akan hilang
            return redirect()->route('booking.show', $bookingId)
                            ->with('success', 'Kursi berhasil dipesan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui status menjadi dibatalkan (soft/logic cancel).
     */
    public function cancel($id)
    {
        $booking = Booking::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($booking->status === 'pending') {
            $booking->update(['status' => 'cancelled']);
            return back()->with('success', 'Pesanan berhasil dibatalkan.');
        }

        return back()->with('error', 'Pesanan tidak dapat dibatalkan.');
    }

    /**
     * Proses simulasi pembayaran.
     */
    public function pay($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->status !== 'pending') {
            return back()->with('error', 'Tiket sudah dibayar atau dibatalkan.');
        }

        $booking->update(['status' => 'success']);

        // Kirim Email Konfirmasi
        $user = Auth::user();
        if ($user && $user->email) {
            Mail::to($user->email)
                ->bcc('admin_rail@example.com')
                ->send(new TicketPaidMail($booking));
        }

        return redirect()->back()->with('success', 'Pembayaran berhasil!');
    }

    /**
     * Detail tiket/invoice.
     */
    public function show($id)
    {
        // WAJIB: Pastikan ada with('passengers.seat') supaya data penumpang & kursi terambil
        $booking = \App\Models\Booking::with(['passengers.seat', 'schedule.train', 'schedule.originStation', 'schedule.destinationStation'])
                    ->findOrFail($id);

        return view('tickets.show', compact('booking'));
    }

    /**
     * Menghapus pesanan dari database.
     */
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $booking->delete();

        return redirect()->route('tickets.index')->with('success', 'Data pesanan berhasil dihapus.');
    }
}
