@extends('layouts.app')

@section('title', 'Pilih Kursi')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 transition-colors duration-300">
    <div class="bg-white dark:bg-slate-900 p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 transition-colors">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Pilih Kursi Kereta</h2>
                <p class="text-gray-500 dark:text-slate-400">{{ $schedule->train->name }} - {{ $schedule->train->class }}</p>
                <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-bold uppercase tracking-wider">
                    Pilih {{ count(session('booking_passengers', [])) }} Kursi
                </div>
            </div>
            <div class="text-right bg-orange-50 dark:bg-orange-900/20 p-4 rounded-xl border border-orange-100 dark:border-orange-900/30">
                <p class="text-xs text-orange-400 dark:text-orange-300 font-semibold uppercase tracking-wider">Harga per Kursi</p>
                <p class="text-2xl font-black text-orange-500 dark:text-orange-400">Rp {{ number_format($schedule->price, 0, ',', '.') }}</p>
            </div>
        </div>

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-xl border border-red-200 dark:border-red-900/50">
                {{ session('error') }}
            </div>
        @endif

        {{-- Legenda Status --}}
        <div class="flex flex-wrap gap-6 mb-8 py-4 border-y border-gray-50 dark:border-slate-800 text-sm font-medium">
            <div class="flex items-center gap-2">
                <div class="w-5 h-5 bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-slate-700 rounded-md"></div>
                <span class="text-gray-600 dark:text-slate-400">Tersedia</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-5 h-5 bg-red-500 rounded-md"></div>
                <span class="text-gray-600 dark:text-slate-400">Terisi (Paid/Pending)</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-5 h-5 bg-blue-600 rounded-md"></div>
                <span class="text-gray-600 dark:text-slate-400">Pilihanmu</span>
            </div>
        </div>

        <form action="{{ route('tickets.store') }}" method="POST" id="seatForm" autocomplete="off">
            @csrf
            <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">

            <div class="space-y-12">
                @foreach($schedule->train->carriages as $carriage)
                    <div class="relative">
                        <div class="flex items-center gap-4 mb-6">
                            <span class="h-px flex-1 bg-gray-100 dark:bg-slate-800"></span>
                            <h3 class="text-lg font-bold text-gray-400 dark:text-slate-500 uppercase tracking-widest">{{ $carriage->name }}</h3>
                            <span class="h-px flex-1 bg-gray-100 dark:bg-slate-800"></span>
                        </div>

                        {{-- Seat Layout --}}
                        <div class="grid grid-cols-5 gap-2 max-w-sm mx-auto bg-gray-100/50 dark:bg-slate-800/30 p-6 rounded-3xl border border-dashed border-gray-200 dark:border-slate-700">
                            @foreach($carriage->seats as $seat)
                                @php
                                    // Sinkronisasi tipe data untuk pengecekan in_array
                                    $currentSeatId = (int)$seat->id;
                                    $bookedIds = array_map('intval', $bookedSeatIds);
                                    $isBooked = in_array($currentSeatId, $bookedIds);
                                @endphp

                                <label class="relative group col-span-1">
                                    <input type="checkbox" name="seat_ids[]" value="{{ $seat->id }}"
                                        class="seat-checkbox peer hidden" {{ $isBooked ? 'disabled' : '' }}>

                                    {{-- UI Kursi --}}
                                    <div class="w-full aspect-square flex items-center justify-center rounded-lg border-2 transition-all duration-200 cursor-pointer
                                        {{ $isBooked
                                            ? 'bg-red-500 border-red-600 text-white cursor-not-allowed opacity-100'
                                            : 'bg-white dark:bg-slate-900 border-gray-200 dark:border-slate-700 text-gray-600 dark:text-slate-300 hover:border-blue-400 peer-checked:bg-blue-600 peer-checked:border-blue-600 peer-checked:text-white'
                                        }}">
                                        <span class="text-xs font-black">{{ $seat->seat_number }}</span>
                                    </div>
                                </label>

                                {{-- Lorong Tengah (Setiap setelah 2 kursi dalam pola 2-1-2) --}}
                                @if($loop->iteration % 2 == 0 && $loop->iteration % 4 != 0)
                                    <div class="col-span-1 flex items-center justify-center">
                                        <div class="w-1 h-2/3 bg-gray-200 dark:bg-slate-700 rounded-full opacity-30"></div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Footer Action --}}
            <div class="mt-12 pt-8 border-t border-gray-100 dark:border-slate-800 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="text-gray-400 dark:text-slate-500 text-sm italic">
                    * <span id="status-text">Pilih <span id="required-count">{{ count(session('booking_passengers', [])) }}</span> kursi lagi.</span>
                </div>
                <button type="submit" id="submitBtn" disabled
                    class="w-full md:w-auto bg-blue-600 text-white px-12 py-4 rounded-2xl font-black text-lg transition-all shadow-xl opacity-50 cursor-not-allowed uppercase tracking-wider hover:scale-105 active:scale-95 disabled:hover:scale-100">
                    Konfirmasi Pesanan ➜
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const maxSeats = Number("{{ count(session('booking_passengers', [])) }}");
            const checkboxes = document.querySelectorAll('.seat-checkbox:not(:disabled)');
            const submitBtn = document.getElementById('submitBtn');
            const statusText = document.getElementById('status-text');

            function updateUI() {
                const checkedCount = document.querySelectorAll('.seat-checkbox:checked').length;
                const remaining = maxSeats - checkedCount;

                if (remaining > 0) {
                    statusText.innerHTML = `Pilih <span class="font-bold text-blue-500">${remaining}</span> kursi lagi.`;
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    statusText.innerHTML = `<span class="text-green-500 font-bold">✓ Kursi sudah lengkap!</span>`;
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const checkedCount = document.querySelectorAll('.seat-checkbox:checked').length;

                    if (checkedCount > maxSeats) {
                        this.checked = false;
                        alert('Maaf, kamu hanya bisa memilih ' + maxSeats + ' kursi.');
                    }
                    updateUI();
                });
            });

            // Prevent form persistence on refresh
            document.getElementById('seatForm').reset();
            updateUI();
        });
    </script>
</div>
@endsection
