<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Booking;
use App\Models\Station;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Mail\TicketPaidMail;
use Illuminate\Support\Facades\Mail;

class TicketController extends Controller
{
    public function index()
    {
        $stations = Station::all();
        $schedules = collect();
        return view('tickets.index', compact('stations', 'schedules'));
    }

    public function search(Request $request)
    {
        $stations = Station::all();
        $schedules = Schedule::where('origin_station_id', $request->origin)
            ->where('destination_station_id', $request->destination)
            ->whereDate('departure_time', $request->date)
            ->with(['train', 'originStation', 'destinationStation'])
            ->get();

        return view('tickets.index', compact('schedules', 'stations'));
    }

    public function selectSeats($scheduleId)
    {
        $schedule = Schedule::with('train.carriages.seats')->findOrFail($scheduleId);

        $bookedSeatIds = Booking::where('schedule_id', $scheduleId)
            ->whereIn('status', ['pending', 'success'])
            ->pluck('seat_id')
            ->toArray();

        return view('tickets.select-seat', compact('schedule', 'bookedSeatIds'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'seat_id' => 'required|exists:seats,id',
        ]);

        return DB::transaction(function () use ($request) {
            $isBooked = Booking::where('schedule_id', $request->schedule_id)
                ->where('seat_id', $request->seat_id)
                ->whereIn('status', ['pending', 'success'])
                ->exists();

            if ($isBooked) {
                return back()->with('error', 'Maaf, kursi ini sudah dipesan.');
            }

            $booking = Booking::create([
                'booking_code' => 'KAI-' . strtoupper(Str::random(8)),
                'user_id' => Auth::id(),
                'schedule_id' => $request->schedule_id,
                'seat_id' => $request->seat_id,
                'status' => 'pending',
            ]);

            return redirect()->route('booking.show', $booking->id)
                             ->with('success', 'Booking berhasil! Silahkan bayar.');
        });
    }

    public function pay($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'success']);

        $user = Auth::user();

        if ($user && $user->email) {
            // Baris ini yang diubah untuk menambahkan BCC
            Mail::to($user->email) // Email ke Pembeli
                ->bcc('email_asli_admin_kamu@gmail.com') // Email salinan ke kamu sebagai Admin
                ->send(new TicketPaidMail($booking));
        }

        return redirect()->back()->with('success', 'Pembayaran berhasil! Tiket dikirim ke email.');
    }

    public function show($id)
    {
        $booking = Booking::with(['schedule.train', 'schedule.originStation', 'schedule.destinationStation', 'seat.carriage'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('tickets.show', compact('booking'));
    }
}
