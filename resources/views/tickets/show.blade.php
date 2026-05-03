@extends('layouts.app')

@section('title', 'Detail Tiket - RailExpress')

@section('content')
<div class="max-w-3xl mx-auto py-10 px-4">

    <!-- Notifikasi Sukses -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-800 rounded-2xl flex items-center shadow-sm notif">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">

        <!-- Header Tiket -->
        <div class="{{ $booking->status == 'pending' ? 'bg-gradient-to-r from-orange-500 to-orange-600' : 'bg-gradient-to-r from-blue-600 to-indigo-700' }} px-8 py-8 text-white flex justify-between items-center">
            <div>
                <p class="text-white/80 text-xs font-semibold uppercase tracking-widest mb-1">Kode Booking</p>
                <h2 class="text-4xl font-black tracking-tight">{{ $booking->booking_code }}</h2>
            </div>
            <div class="text-right">
                <span class="px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider {{ $booking->status == 'pending' ? 'bg-white/20 text-white' : 'bg-green-400 text-white' }}">
                    ● {{ $booking->status }}
                </span>
            </div>
        </div>

        <div class="p-8">
            <!-- Informasi Utama Kereta -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                <div>
                    <label class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Informasi Kereta</label>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $booking->schedule->train->name }}</p>
                    <p class="text-sm font-medium text-gray-500">{{ $booking->schedule->train->class }} Class</p>
                </div>
                <div>
                    <label class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Detail Tempat Duduk</label>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $booking->seat->carriage->name }}</p>
                    <p class="text-xl text-indigo-600 font-black tracking-tight">Kursi {{ $booking->seat->seat_number }}</p>
                </div>
            </div>

            <div class="relative flex items-center mb-10">
                <div class="flex-grow border-t border-dashed border-gray-200"></div>
                <span class="flex-shrink mx-4 text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                </span>
                <div class="flex-grow border-t border-dashed border-gray-200"></div>
            </div>

            <!-- Rute Perjalanan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-10">
                <div class="relative pl-6 border-l-4 border-blue-500">
                    <label class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Keberangkatan</label>
                    <p class="text-xl font-bold text-gray-800 mt-1">{{ $booking->schedule->originStation->name }}</p>
                    <p class="text-sm font-semibold text-gray-600">{{ \Carbon\Carbon::parse($booking->schedule->departure_time)->format('d M Y') }}</p>
                    <p class="text-lg font-black text-blue-600">{{ \Carbon\Carbon::parse($booking->schedule->departure_time)->format('H:i') }}</p>
                </div>

                <div class="relative pl-6 border-l-4 border-indigo-500 md:text-right md:border-l-0 md:border-r-4 md:pr-6">
                    <label class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Kedatangan</label>
                    <p class="text-xl font-bold text-gray-800 mt-1">{{ $booking->schedule->destinationStation->name }}</p>
                    <p class="text-sm font-semibold text-gray-600">{{ \Carbon\Carbon::parse($booking->schedule->arrival_time)->format('d M Y') }}</p>
                    <p class="text-lg font-black text-indigo-600">{{ \Carbon\Carbon::parse($booking->schedule->arrival_time)->format('H:i') }}</p>
                </div>
            </div>

            <!-- Footer (Harga & Aksi) -->
            <div class="bg-gray-50 -mx-8 -mb-8 p-8 flex flex-col md:flex-row justify-between items-center border-t border-gray-100">
                <div class="mb-6 md:mb-0 text-center md:text-left">
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Total Harga</p>
                    <p class="text-3xl font-black text-orange-500">Rp {{ number_format($booking->schedule->price, 0, ',', '.') }}</p>
                </div>

                <div class="w-full md:w-auto">
                    @if($booking->status == 'pending')
                        <form action="{{ route('booking.pay', $booking->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-orange-500 text-white px-8 py-3 rounded-xl font-bold uppercase hover:bg-orange-600 transition">
                                Bayar Sekarang
                            </button>
                        </form>
                    @else
                        {{-- Tombol Cetak Memanggil Fungsi JS --}}
                        <button onclick="printAndRedirect()" class="bg-emerald-500 text-white px-8 py-3 rounded-xl font-bold uppercase hover:bg-emerald-600 transition shadow-lg shadow-emerald-100">
                            Cetak Tiket
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 text-center no-print">
        <a href="{{ route('tickets.index') }}" class="text-gray-400 hover:text-gray-600 font-bold text-sm uppercase tracking-widest transition flex items-center justify-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Beranda
        </a>
    </div>
</div>

{{-- SCRIPT REDIRECT OTOMATIS --}}
<script>
    function printAndRedirect() {
        window.print();
    }

    // Listener ini akan mendeteksi ketika jendela print ditutup (baik klik print atau cancel)
    window.addEventListener('afterprint', function() {
        setTimeout(function() {
            window.location.href = "{{ route('tickets.index') }}";
        }, 500); // jeda 0.5 detik agar transisi mulus
    });
</script>

<style>
    @media print {
        /* Hilangkan elemen navigasi, tombol, dan link saat cetak */
        nav, button, a, .notif, .no-print {
            display: none !important;
        }
        body {
            background: white;
        }
        .max-w-3xl {
            max-width: 100% !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .shadow-xl {
            box-shadow: none !important;
            border: 1px solid #eee !important;
        }
    }
</style>
@endsection
