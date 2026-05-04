@extends('layouts.app')

@section('title', 'Input Data Penumpang')

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4">
    <div class="bg-white dark:bg-slate-900 p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 transition-colors">

        {{-- Header Info --}}
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Data Penumpang</h2>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-bold uppercase tracking-wider">
                    {{ $schedule->train->class }}
                </span>
                <p class="text-gray-500 dark:text-slate-400 font-medium">{{ $schedule->train->name }}</p>
            </div>
        </div>

        <form action="{{ route('tickets.processPassengers', $schedule->id) }}" method="POST">
            @csrf

            {{-- Menampilkan Error Validasi --}}
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-xl border border-red-100 dark:border-red-900/30">
                    <ul class="list-disc list-inside text-sm font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-8">
                @php
                    // Ambil jumlah penumpang dari request (hasil pencarian)
                    $count = request('passenger_count', 1);
                @endphp

                @for ($i = 0; $i < $count; $i++)
                    <div class="p-6 rounded-2xl border border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800/30">
                        <div class="flex items-center gap-4 mb-5">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 text-white text-xs font-black">
                                {{ $i + 1 }}
                            </span>
                            <h3 class="text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-widest">Penumpang {{ $i + 1 }}</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Input Nama --}}
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 dark:text-slate-500 uppercase">Nama Lengkap</label>
                                <input type="text" name="passengers[{{ $i }}][name]" required
                                    class="w-full rounded-xl border-gray-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all placeholder:text-gray-300 dark:placeholder:text-slate-700"
                                    placeholder="Sesuai KTP/Paspor"
                                    value="{{ old("passengers.$i.name") }}">
                            </div>

                            {{-- Input NIK --}}
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 dark:text-slate-500 uppercase">Nomor Identitas (NIK)</label>
                                <input type="text" name="passengers[{{ $i }}][nik]" required
                                    class="w-full rounded-xl border-gray-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all placeholder:text-gray-300 dark:placeholder:text-slate-700"
                                    placeholder="16 Digit NIK"
                                    value="{{ old("passengers.$i.nik") }}">
                            </div>
                        </div>
                    </div>
                @endfor
            </div>

            <button type="submit" class="w-full mt-10 bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-black text-lg transition-all shadow-lg shadow-blue-500/20 active:scale-95 uppercase tracking-wider">
                Lanjut Pilih {{ $count }} Kursi ➜
            </button>
        </form>
    </div>
</div>
@endsection
