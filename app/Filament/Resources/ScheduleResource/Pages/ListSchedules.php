<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use App\Models\Schedule;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class ListSchedules extends ListRecords
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('generateMonthly') // Pakai backslash di depan
                ->label('Generate Jadwal 30 Hari')
                ->icon('heroicon-o-calendar-days')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\Select::make('train_id')
                        ->relationship('train', 'name')
                        ->required()
                        ->label('Kereta'),
                    \Filament\Forms\Components\Select::make('origin_station_id')
                        ->relationship('originStation', 'name')
                        ->required()
                        ->label('Stasiun Asal'),
                    \Filament\Forms\Components\Select::make('destination_station_id')
                        ->relationship('destinationStation', 'name')
                        ->required()
                        ->label('Stasiun Tujuan'),
                    Forms\Components\TimePicker::make('departure_hour')
                        ->required()
                        ->label('Jam Keberangkatan')
                        ->native(false)
                        ->format('H:i')
                        ->displayFormat('H:i'),
                    Forms\Components\TimePicker::make('arrival_hour')
                        ->required()
                        ->label('Jam Tiba')
                        ->native(false)
                        ->format('H:i')
                        ->displayFormat('H:i'),
                    \Filament\Forms\Components\DatePicker::make('start_date')
                        ->default(now())
                        ->required()
                        ->label('Mulai Tanggal'),
                    \Filament\Forms\Components\TextInput::make('price')
                        ->numeric()
                        ->prefix('Rp')
                        ->required()
                        ->label('Harga Tiket'),
                ])
                ->action(function (array $data) {
                    $startDate = \Carbon\Carbon::parse($data['start_date']);

                    for ($i = 0; $i < 30; $i++) {
                        $currentDate = $startDate->copy()->addDays($i)->format('Y-m-d');

                        \App\Models\Schedule::create([
                            'train_id' => $data['train_id'],
                            'origin_station_id' => $data['origin_station_id'],
                            'destination_station_id' => $data['destination_station_id'],
                            'departure_time' => $currentDate . ' ' . $data['departure_hour'],
                            'arrival_time' => $currentDate . ' ' . $data['arrival_hour'],
                            'price' => $data['price'],
                        ]);
                    }

                    \Filament\Notifications\Notification::make()
                        ->title('Berhasil!')
                        ->body('Jadwal untuk 30 hari ke depan telah dibuat.')
                        ->success()
                        ->send();
                }),

            \Filament\Actions\CreateAction::make()
                ->label('Buat Jadwal Baru')
                ->modalWidth('lg'),
        ];
    }
}
