<?php

namespace App\Filament\Resources\SeatResource\Pages;

use App\Filament\Resources\SeatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSeats extends ListRecords
{
    protected static string $resource = SeatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 1. Tombol Create Manual
            \Filament\Actions\CreateAction::make()
                ->modalWidth('md'),

            // 2. Tombol Generate Otomatis
            \Filament\Actions\Action::make('generateSeats')
                ->label('Generate Seats Otomatis')
                ->color('success')
                ->icon('heroicon-o-cpu-chip')
                ->modalWidth('md')
                ->form([
                    \Filament\Forms\Components\Select::make('carriage_id')
                        ->label('Pilih Gerbong')
                        ->relationship('carriage', 'name')
                        ->required(),
                    \Filament\Forms\Components\TextInput::make('rows')
                        ->label('Jumlah Baris')
                        ->numeric()
                        ->default(10)
                        ->required(),
                    \Filament\Forms\Components\TextInput::make('cols')
                        ->label('Kursi per Baris')
                        ->numeric()
                        ->default(4)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $alphabet = range('A', 'Z');

                    for ($i = 1; $i <= $data['rows']; $i++) {
                        for ($j = 0; $j < $data['cols']; $j++) {
                            $seatNumber = $i . $alphabet[$j];

                            \App\Models\Seat::create([
                                'carriage_id' => $data['carriage_id'],
                                'seat_number' => $seatNumber,
                                'is_active' => true,
                            ]);
                        }
                    }

                    \Filament\Notifications\Notification::make()
                        ->title('Berhasil!')
                        ->success()
                        ->body('Kursi berhasil di-generate.')
                        ->send();
                }), // Pastikan ada koma di sini sebelum tombol berikutnya

            // 3. Tombol Reset
            \Filament\Actions\Action::make('resetSeats')
                ->label('Reset Kursi')
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->form([
                    \Filament\Forms\Components\Select::make('carriage_id')
                        ->label('Pilih Gerbong untuk Dikosongkan')
                        ->relationship('carriage', 'name')
                        ->required(),
                ])
                ->action(function (array $data) {
                    \App\Models\Seat::where('carriage_id', $data['carriage_id'])->delete();

                    \Filament\Notifications\Notification::make()
                        ->title('Dibersihkan!')
                        ->success()
                        ->send();
                }),
        ];
    }
}
