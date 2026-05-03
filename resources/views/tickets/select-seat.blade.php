@extends('layouts.app')

@section('title', 'Pilih Kursi')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Pilih Kursi Kereta</h2>
                <p class="text-gray-500">{{ $schedule->train->name }} - {{ $schedule->train->class }}</p>
            </div>
            <div class="text-right bg-orange-50 p-4 rounded-xl border border-orange-100">
                <p class="text-xs text-orange-400 font-semibold uppercase tracking-wider">Harga per Kursi</p>
                <p class="text-2xl font-black text-orange-500">Rp {{ number_format($schedule->price, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Pesan Error/Sukses -->
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-xl border border-red-200">
                {{ session('error') }}
            </div>
        @endif

        <!-- Legenda -->
        <div class="flex flex-wrap gap-6 mb-8 py-4 border-y border-gray-50 text-sm font-medium">
            <div class="flex items-center gap-2">
                <div class="w-5 h-5 bg-gray-50 border border-gray-300 rounded-md"></div>
                <span class="text-gray-600">Tersedia</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-5 h-5 bg-red-500 rounded-md shadow-sm shadow-red-200"></div>
                <span class="text-gray-600">Terisi</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-5 h-5 bg-blue-600 rounded-md shadow-sm shadow-blue-200"></div>
                <span class="text-gray-600">Pilihanmu</span>
            </div>
        </div>

        <form action="{{ route('tickets.store') }}" method="POST">
            @csrf
            <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">

            <div class="space-y-12">
                {{-- Loop Gerbong (Carriages) --}}
                @foreach($schedule->train->carriages as $carriage)
                    <div class="relative">
                        <div class="flex items-center gap-4 mb-6">
                            <span class="h-px flex-1 bg-gray-100"></span>
                            <h3 class="text-lg font-bold text-gray-400 uppercase tracking-widest">{{ $carriage->name }}</h3>
                            <span class="h-px flex-1 bg-gray-100"></span>
                        </div>

                        <!-- Area Kursi -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 max-w-md mx-auto">
                            @foreach($carriage->seats as $seat)
                                @php
                                    $isBooked = in_array($seat->id, $bookedSeatIds);
                                @endphp

                                <label class="relative group">
                                    <input type="radio" name="seat_id" value="{{ $seat->id }}"
                                        class="peer hidden"
                                        {{ $isBooked ? 'disabled' : '' }} required>

                                    <div class="w-full py-4 text-center rounded-xl border-2 transition-all duration-200 cursor-pointer
                                        {{ $isBooked
                                            ? 'bg-red-500 border-red-600 text-white cursor-not-allowed shadow-inner opacity-80'
                                            : 'bg-gray-50 border-gray-200 text-gray-600 hover:border-blue-400 peer-checked:bg-blue-600 peer-checked:border-blue-600 peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-blue-200'
                                        }}">
                                        <span class="text-sm font-black">{{ $seat->seat_number }}</span>
                                    </div>

                                    @if($isBooked)
                                        <div class="absolute inset-0 z-10" title="Kursi Sudah Terisi"></div>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-12 pt-8 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="text-gray-400 text-sm italic">
                    * Pastikan pilihan kursi sudah benar sebelum konfirmasi.
                </div>
                <button type="submit" class="w-full md:w-auto bg-blue-600 text-white px-12 py-4 rounded-2xl font-black text-lg hover:bg-blue-700 transition-all shadow-xl shadow-blue-100 transform hover:-translate-y-1 active:scale-95 uppercase tracking-wider">
                    Konfirmasi Pesanan ➜
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
