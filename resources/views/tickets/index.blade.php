@extends('layouts.app')

@section('title', 'Cari Jadwal Kereta - RailExpress')

@section('content')
<div class="max-w-6xl mx-auto space-y-10 pb-20 px-4">

    <!-- Page Header -->
    <div class="flex flex-col gap-2">
        <h1 class="text-2xl font-bold tracking-tight text-slate-950 dark:text-white sm:text-3xl">
            Cari <span class="text-blue-600">Jadwal</span> Kereta
        </h1>
        <p class="text-sm text-slate-500 dark:text-zinc-500">
            Temukan perjalanan terbaik bersama RailExpress.
        </p>
    </div>

    <!-- Search Card -->
    <div class="bg-white dark:bg-[#18181b] border border-slate-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden transition-colors">
        <form action="{{ route('tickets.search') }}" method="GET" class="p-6 md:p-8">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6">

                <!-- 1. Stasiun Asal -->
                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700 dark:text-zinc-300">Stasiun Asal</label>
                    <select name="origin" class="block w-full rounded-lg border-0 bg-slate-50 dark:bg-zinc-900/50 py-2.5 text-sm ring-1 ring-inset ring-slate-200 dark:ring-zinc-800 focus:ring-2 focus:ring-inset focus:ring-blue-600 transition dark:text-white">
                        @foreach($stations as $station)
                            <option value="{{ $station->id }}" {{ request('origin') == $station->id ? 'selected' : '' }}>
                                {{ $station->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Stasiun Tujuan -->
                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700 dark:text-zinc-300">Stasiun Tujuan</label>
                    <select name="destination" class="block w-full rounded-lg border-0 bg-slate-50 dark:bg-zinc-900/50 py-2.5 text-sm ring-1 ring-inset ring-slate-200 dark:ring-zinc-800 focus:ring-2 focus:ring-inset focus:ring-blue-600 transition dark:text-white">
                        @foreach($stations as $station)
                            <option value="{{ $station->id }}" {{ request('destination') == $station->id ? 'selected' : '' }}>
                                {{ $station->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 3. Tanggal Pergi -->
                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700 dark:text-zinc-300">Tanggal Pergi</label>
                    <input type="date" name="date"
                        value="{{ request('date', date('Y-m-d')) }}"
                        class="block w-full rounded-lg border-0 bg-slate-50 dark:bg-zinc-900/50 py-2.5 text-sm ring-1 ring-inset ring-slate-200 dark:ring-zinc-800 focus:ring-2 focus:ring-inset focus:ring-blue-600 transition dark:text-white [color-scheme:light] dark:[color-scheme:dark]">
                </div>

                <!-- 4. Jumlah Penumpang -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">Penumpang</label>
                    <input type="number" name="passenger_count" min="1" max="4" value="{{ request('passenger_count', 1) }}"
                        class="block w-full rounded-lg border-0 bg-slate-50 dark:bg-zinc-900/50 py-2.5 text-sm ring-1 ring-inset ring-slate-200 dark:ring-zinc-800 focus:ring-2 focus:ring-inset focus:ring-blue-600 transition dark:text-white">
                </div>

                <!-- 5. Button Cari -->
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 rounded-lg text-sm transition-all shadow-sm uppercase tracking-widest">
                        Cari Kereta
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- List Jadwal -->
    <div class="space-y-4">
        @forelse($schedules as $schedule)
            <div class="bg-white dark:bg-[#18181b] border border-slate-200 dark:border-zinc-800 rounded-xl p-6 transition-all hover:border-blue-500/50 group">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <!-- Kereta -->
                    <div class="flex flex-col gap-1 w-full md:w-1/4">
                        <span class="text-xs font-black uppercase tracking-[0.2em] text-blue-600">
                            {{ $schedule->train->class }}
                        </span>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                            {{ $schedule->train->name }}
                        </h3>
                    </div>

                    <!-- Rute & Waktu -->
                    <div class="flex flex-1 items-center justify-between w-full px-0 md:px-10">
                        <div class="text-center md:text-left">
                            <p class="text-xl font-bold dark:text-white">{{ \Carbon\Carbon::parse($schedule->departure_time)->format('H:i') }}</p>
                            <p class="text-xs text-slate-500 uppercase">{{ $schedule->originStation->name }}</p>
                        </div>
                        <div class="flex-1 flex flex-col items-center px-4">
                            <span class="text-[10px] text-slate-400 font-medium mb-2 uppercase italic">Durasi</span>
                            <div class="h-[1px] w-full bg-slate-200 dark:bg-zinc-800 relative">
                                <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 text-slate-300 dark:text-zinc-700">
                                    <svg class="w-4 h-4 rotate-90" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="text-center md:text-right">
                            <p class="text-xl font-bold dark:text-white">{{ \Carbon\Carbon::parse($schedule->arrival_time)->format('H:i') }}</p>
                            <p class="text-xs text-slate-500 uppercase">{{ $schedule->destinationStation->name }}</p>
                        </div>
                    </div>

                    <!-- Harga & Aksi -->
                    <div class="flex flex-col md:items-end gap-3 w-full md:w-1/4 border-t md:border-t-0 md:border-l border-slate-100 dark:border-zinc-800 pt-4 md:pt-0 md:pl-8">
                        <div class="text-right">
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Harga</span>
                            <p class="text-xl font-black text-blue-600 dark:text-blue-500">
                                Rp {{ number_format($schedule->price, 0, ',', '.') }}
                            </p>
                        </div>

                        <a href="{{ route('tickets.passengers', ['schedule' => $schedule->id, 'passenger_count' => request('passenger_count', 1)]) }}"
                           class="bg-slate-900 dark:bg-white text-white dark:text-black text-xs font-bold py-2.5 px-4 rounded-lg hover:bg-blue-600 dark:hover:bg-blue-500 dark:hover:text-white transition-all text-center uppercase tracking-widest w-full">
                            Pesan {{ request('passenger_count', 1) }} Tiket
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center border-2 border-dashed border-slate-200 dark:border-zinc-800 rounded-xl py-24 bg-slate-50/50 dark:bg-zinc-900/10 text-center">
                <h2 class="text-lg font-bold text-slate-950 dark:text-white">Jadwal tidak ditemukan</h2>
                <p class="text-sm text-slate-500 dark:text-zinc-500 mt-1">Gunakan kriteria pencarian lain atau pilih tanggal berbeda.</p>
            </div>
        @endforelse
    </div>

    <!-- Riwayat Pesanan Saya -->
    @auth
    <div class="mt-16 pt-10 border-t border-slate-200 dark:border-zinc-800">
        <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6">Riwayat Pesanan Terakhir</h3>

        @forelse($myBookings as $booking)
            <div class="bg-white dark:bg-[#18181b] border border-slate-200 dark:border-zinc-800 p-6 rounded-2xl mb-4 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4 {{ $booking->status == 'cancelled' ? 'opacity-60' : '' }}">
                <div class="flex-1 w-full md:w-auto">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-xs font-black uppercase tracking-widest text-blue-500">{{ $booking->booking_code }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                            {{ $booking->status == 'pending' ? 'bg-orange-100 text-orange-600' :
                            ($booking->status == 'success' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600') }}">
                            {{ $booking->status }}
                        </span>
                    </div>
                    <h4 class="font-bold text-gray-800 dark:text-white">{{ $booking->schedule->train->name }}</h4>
                    <p class="text-sm text-gray-500">
                        {{ $booking->schedule->originStation->name }} ➜ {{ $booking->schedule->destinationStation->name }}
                    </p>
                </div>

                <div class="flex flex-row items-center gap-6 w-full md:w-auto justify-between md:justify-end">
                    <div class="md:text-right">
                        @if($booking->status != 'cancelled')
                            <p class="text-xs text-gray-400 uppercase font-bold">Total Bayar</p>
                            <p class="font-black text-gray-800 dark:text-white">
                                Rp {{ number_format($booking->total_price ?? ($booking->schedule->price * $booking->passengers->count()), 0, ',', '.') }}
                            </p>
                        @else
                            <span class="text-xs text-slate-500 italic">Pesanan Dibatalkan</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('booking.show', $booking->id) }}" class="text-xs font-bold bg-slate-100 dark:bg-zinc-800 dark:text-white px-4 py-2 rounded-lg hover:bg-slate-200 transition">Detail</a>

                        @if($booking->status === 'pending')
                            <form action="{{ route('booking.cancel', $booking->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')">
                                @csrf
                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium">Cancel</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-gray-50 dark:bg-zinc-900/30 rounded-3xl border-2 border-dashed border-gray-200 dark:border-zinc-800">
                <p class="text-gray-400 italic">Belum ada riwayat pemesanan.</p>
            </div>
        @endforelse
    </div>
    @endauth

</div>
@endsection
