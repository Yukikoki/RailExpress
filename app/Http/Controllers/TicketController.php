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
        // 1. Validasi input dari form pemilihan kursi
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'seat_ids' => 'required|array',
        ]);

        // 2. Ambil data penumpang yang sebelumnya disimpan di session
        $passengers = session('booking_passengers');

        // 3. Validasi kecocokan jumlah kursi dan jumlah penumpang
        if (count($request->seat_ids) !== count($passengers)) {
            return back()->with('error', 'Jumlah kursi tidak sesuai dengan jumlah penumpang.');
        }

        // 4. Ambil data jadwal untuk mendapatkan harga asli dari Panel Admin
        $schedule = \App\Models\Schedule::findOrFail($request->schedule_id);

        try {
            DB::beginTransaction();

            // 5. Masukkan data ke tabel bookings
            // Status diset 'pending' agar header berwarna Orange
            $bookingId = DB::table('bookings')->insertGetId([
                'booking_code' => 'REX-' . strtoupper(Str::random(8)),
                'user_id'      => Auth::id(),
                'schedule_id'  => $request->schedule_id,

                // Perbaikan: Harga sekarang dinamis (Jumlah Penumpang x Harga di Jadwal)
                'total_price'  => count($request->seat_ids) * $schedule->price,

                'status'       => 'pending',
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            // 6. Masukkan detail penumpang dan kursi ke tabel booking_passengers
            foreach ($request->seat_ids as $index => $seatId) {
                DB::table('booking_passengers')->insert([
                    'booking_id' => $bookingId,
                    'seat_id'    => $seatId,
                    'name'       => $passengers[$index]['name'],
                    'nik'        => $passengers[$index]['nik'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                /**
                 * CATATAN: Baris update 'is_available' dihapus karena
                 * kolom tersebut tidak ada di tabel 'seats' Anda.
                 * Sistem pengecekan kursi sudah ditangani oleh join table di selectSeats.
                 */
            }

            DB::commit();

            // 7. Bersihkan session setelah transaksi berhasil
            session()->forget('booking_passengers');

            // 8. Redirect ke halaman detail tiket (Header akan otomatis Orange)
            return redirect()->route('booking.show', $bookingId)
                            ->with('success', 'Kursi berhasil dipesan! Silakan selesaikan pembayaran.');

        } catch (\Exception $e) {
            // Jika ada error, batalkan semua perubahan database
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui status menjadi dibatalkan (soft/logic cancel).
     */
    public function cancel($id)
{
    // 1. Cari booking milik user yang login
    $booking = Booking::where('id', $id)
        ->where('user_id', Auth::id())
        ->firstOrFail();

    // 2. Hanya bisa cancel jika status masih 'pending'
    if ($booking->status === 'pending') {
        try {
            DB::beginTransaction();

            // Ubah status jadi cancelled
            $booking->update(['status' => 'cancelled']);

            // Opsional: Jika kamu ingin menghapus data penumpang agar kursi langsung kosong di peta:
            // DB::table('booking_passengers')->where('booking_id', $id)->delete();

            DB::commit();
            return redirect()->route('tickets.index')->with('success', 'Pesanan berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan pesanan.');
        }
    }

    return back()->with('error', 'Pesanan ini tidak dapat dibatalkan.');
}

    /**
     * Proses simulasi pembayaran.
     */
    public function pay($id)
    {
        // 1. Cari booking milik user yang sedang login untuk keamanan
        $booking = Booking::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // 2. Hanya proses jika status masih pending
        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Pesanan ini sudah diproses atau dibatalkan.');
        }

        try {
            DB::beginTransaction();

            // 3. Update status jadi success
            $booking->update(['status' => 'success']);

            // 4. (Opsional) Kirim Email atau Logika Tambahan di sini
            // Mail::to(Auth::user()->email)->send(new TicketPaidMail($booking));

            DB::commit();

            return redirect()->back()->with('success', 'Pembayaran Berhasil! Tiket Anda sekarang aktif dan dapat dicetak.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Detail tiket/invoice.
     */
    public function show($id)
    {
        // Menggunakan Eloquent agar relasi 'passengers.seat' terbaca di Blade
        // Pastikan Model Booking sudah punya relasi passengers()
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

    public function laporan()
    {
        // Mengambil booking yang sukses beserta data penumpang dan jadwalnya
        $bookings = Booking::with(['passengers', 'schedule.train'])
            ->where('status', 'success')
            ->latest()
            ->get();

        // Hitung total pendapatan (opsional untuk ringkasan di bawah tabel)
        $totalPendapatan = $bookings->sum('total_price');

        return view('admin.laporan-pendapatan', compact('bookings', 'totalPendapatan'));
    }
}
