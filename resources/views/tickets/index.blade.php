@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-10">

    <!-- Page Header ala Filament -->
    <div class="flex flex-col gap-2">
        <h1 class="text-2xl font-bold tracking-tight text-slate-950 dark:text-white sm:text-3xl">
            Cari <span class="text-blue-600">Jadwal</span> Kereta
        </h1>
        <p class="text-sm text-slate-500 dark:text-zinc-500">
            Temukan perjalanan terbaik bersama RailExpress.
        </p>
    </div>

    <!-- Search Card (Filament Style) -->
    <div class="bg-white dark:bg-[#18181b] border border-slate-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden transition-colors">
        <form action="{{ route('tickets.search') }}" method="GET" class="p-6 md:p-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

                <!-- Stasiun Asal -->
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

                <!-- Stasiun Tujuan -->
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

                <!-- Input Tanggal dengan Perbaikan Icon Dark Mode -->
                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700 dark:text-zinc-300">Tanggal Pergi</label>
                    <input type="date" name="date"
                        value="{{ request('date', date('Y-m-d')) }}"
                        class="block w-full rounded-lg border-0 bg-slate-50 dark:bg-zinc-900/50 py-2.5 text-sm ring-1 ring-inset ring-slate-200 dark:ring-zinc-800 focus:ring-2 focus:ring-inset focus:ring-blue-600 transition dark:text-white [color-scheme:light] dark:[color-scheme:dark]">
                </div>

                <!-- Button Cari -->
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 rounded-lg text-sm transition-all shadow-sm focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 uppercase tracking-widest">
                        Cari Kereta
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- List Jadwal / Hasil -->
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
                            <p class="text-xs text-slate-500 uppercase tracking-tighter">{{ $schedule->originStation->name }}</p>
                        </div>

                        <div class="flex-1 flex flex-col items-center px-4">
                            <span class="text-[10px] text-slate-400 font-medium mb-2 uppercase italic">Durasi 5j 6mnt</span>
                            <div class="h-[1px] w-full bg-slate-200 dark:bg-zinc-800 relative">
                                <div class="absolute -top-1 left-1/2 -translate-x-1/2">
                                    <svg class="w-3 h-3 text-slate-300 dark:text-zinc-700" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                                </div>
                            </div>
                        </div>

                        <div class="text-center md:text-right">
                            <p class="text-xl font-bold dark:text-white">{{ \Carbon\Carbon::parse($schedule->arrival_time)->format('H:i') }}</p>
                            <p class="text-xs text-slate-500 uppercase tracking-tighter">{{ $schedule->destinationStation->name }}</p>
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
                        <a href="/tickets/{{ $schedule->id }}/select-seat" class="bg-slate-900 dark:bg-white text-white dark:text-black text-xs font-bold py-2 px-4 rounded-lg hover:bg-blue-600 dark:hover:bg-blue-500 dark:hover:text-white transition-all text-center uppercase tracking-widest">
                            Pilih Kursi
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <!-- Empty State Filament Style -->
            <div class="flex flex-col items-center justify-center border-2 border-dashed border-slate-200 dark:border-zinc-800 rounded-xl py-24 bg-slate-50/50 dark:bg-zinc-900/10">
                <div class="p-4 bg-white dark:bg-zinc-900 rounded-2xl shadow-sm border border-slate-100 dark:border-zinc-800 mb-6 text-slate-400 dark:text-zinc-600">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-slate-950 dark:text-white">Jadwal tidak ditemukan</h2>
                <p class="text-sm text-slate-500 dark:text-zinc-500 mt-1">Coba gunakan kriteria pencarian yang berbeda.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
