@extends('layouts.app')

@section('title', 'Detail Tiket - RailExpress')

@section('content')
<div class="max-w-3xl mx-auto py-10 px-4">

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-800 rounded-2xl flex items-center shadow-sm notif">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-800 rounded-2xl flex items-center shadow-sm notif">
            <span class="font-bold">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white dark:bg-[#18181b] rounded-3xl shadow-xl overflow-hidden border border-gray-100 dark:border-zinc-800">

        <!-- Header Tiket -->
        <div class="{{ $booking->status == 'pending' ? 'bg-gradient-to-r from-orange-500 to-orange-600' : ($booking->status == 'cancelled' ? 'bg-gray-600' : 'bg-gradient-to-r from-blue-600 to-indigo-700') }} px-8 py-8 text-white flex justify-between items-center">
            <div>
                <p class="text-white/80 text-[10px] font-semibold uppercase tracking-widest mb-1">Kode Booking</p>
                <h2 class="text-4xl font-black tracking-tight">{{ $booking->booking_code }}</h2>
            </div>
            <div class="text-right">
                <span class="px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider {{ $booking->status == 'pending' ? 'bg-white/20' : 'bg-green-400' }}">
                    ● {{ strtoupper($booking->status) }}
                </span>
            </div>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                <div>
                    <label class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Informasi Kereta</label>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $booking->schedule->train->name }}</p>
                    <p class="text-sm font-medium text-gray-500">{{ $booking->schedule->train->class }} Class</p>
                </div>
                <div>
                    <label class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Detail Penumpang & Kursi</label>
                    <div class="mt-2 space-y-3">
                        @foreach($booking->passengers as $p)
                            <div class="flex items-center justify-between bg-slate-50 dark:bg-zinc-900/50 p-3 rounded-xl border border-slate-100 dark:border-zinc-800">
                                <div>
                                    <p class="text-sm font-bold text-gray-800 dark:text-white">{{ $p->name }}</p>
                                    <p class="text-[10px] text-gray-400">NIK: {{ $p->nik }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] text-gray-400 font-bold uppercase">Kursi</p>
                                    <p class="text-lg font-black text-indigo-600 dark:text-indigo-400">{{ $p->seat->seat_number ?? 'N/A' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Divider Dashed -->
            <div class="relative flex items-center mb-10">
                <div class="flex-grow border-t border-dashed border-gray-200 dark:border-zinc-800"></div>
                <span class="flex-shrink mx-4 text-gray-300 dark:text-zinc-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                </span>
                <div class="flex-grow border-t border-dashed border-gray-200 dark:border-zinc-800"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-10">
                <div class="relative pl-6 border-l-4 border-blue-500">
                    <label class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Keberangkatan</label>
                    <p class="text-xl font-bold text-gray-800 dark:text-white mt-1">{{ $booking->schedule->originStation->name }}</p>
                    <p class="text-sm font-semibold text-gray-600">{{ \Carbon\Carbon::parse($booking->schedule->departure_time)->format('d M Y') }}</p>
                    <p class="text-lg font-black text-blue-600">{{ \Carbon\Carbon::parse($booking->schedule->departure_time)->format('H:i') }}</p>
                </div>
                <div class="relative pl-6 border-l-4 border-indigo-500 md:text-right md:border-l-0 md:border-r-4 md:pr-6">
                    <label class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Kedatangan</label>
                    <p class="text-xl font-bold text-gray-800 dark:text-white mt-1">{{ $booking->schedule->destinationStation->name }}</p>
                    <p class="text-sm font-semibold text-gray-600">{{ \Carbon\Carbon::parse($booking->schedule->arrival_time)->format('d M Y') }}</p>
                    <p class="text-lg font-black text-indigo-600">{{ \Carbon\Carbon::parse($booking->schedule->arrival_time)->format('H:i') }}</p>
                </div>
            </div>

            {{-- Footer Harga & Tombol Aksi --}}
            <div class="bg-gray-50 dark:bg-zinc-900/30 -mx-8 -mb-8 p-8 flex flex-col md:flex-row justify-between items-center border-t border-gray-100 dark:border-zinc-800">
                <div class="mb-6 md:mb-0 text-center md:text-left">
                    <p class="text-xs text-gray-500 uppercase tracking-wider">TOTAL HARGA ({{ $booking->passengers->count() }} PENUMPANG)</p>
                    <h2 class="text-2xl font-black text-orange-500">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</h2>
                </div>

                <div class="flex flex-wrap gap-4 justify-center md:justify-end no-print">
                    @if($booking->status === 'pending')
                        <form action="{{ route('booking.cancel', $booking->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                            @csrf
                            <button type="submit" class="px-6 py-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl font-semibold transition">
                                Batalkan Pesanan
                            </button>
                        </form>

                        <form action="{{ route('booking.pay', $booking->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-black shadow-lg shadow-blue-200 dark:shadow-none transition transform hover:scale-105">
                                Bayar Sekarang
                            </button>
                        </form>
                    @endif

                    @if($booking->status === 'success')
                        <button onclick="cetakTiket()" class="px-8 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl font-black shadow-lg shadow-green-200 dark:shadow-none transition flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Cetak E-Tiket
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 text-center no-print">
        <a href="{{ route('tickets.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-white font-bold text-sm uppercase tracking-widest transition flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Beranda
        </a>
    </div>
</div>

<script>
    function cetakTiket() {
        window.print();
    }
</script>

<style>
    @media print {
        nav, footer, .no-print, .notif, button, a { display: none !important; }
        body { background: white !important; padding: 0; }
        .max-w-3xl { max-width: 100% !important; width: 100% !important; margin: 0 !important; }
        .bg-white { box-shadow: none !important; border: 1px solid #eee !important; }
        .rounded-3xl { border-radius: 0 !important; }
        .bg-gradient-to-r { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>
@endsection
