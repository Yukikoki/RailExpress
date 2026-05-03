<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleResource\Pages;
use App\Models\Schedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Data Master Kereta';

    protected static ?int $navigationSort = 5; // Urutan menu

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        // Pilih Kereta
                        Forms\Components\Select::make('train_id')
                            ->relationship('train', 'name') // 'train' adalah nama fungsi relasi di model Schedule
                            ->label('Kereta Api')
                            ->searchable()
                            ->preload()
                            ->required(),

                        // Stasiun Asal
                        Forms\Components\Select::make('origin_station_id')
                            ->relationship('originStation', 'name')
                            ->label('Stasiun Asal')
                            ->searchable()
                            ->preload()
                            ->required(),

                        // Stasiun Tujuan
                        Forms\Components\Select::make('destination_station_id')
                            ->relationship('destinationStation', 'name')
                            ->label('Stasiun Tujuan')
                            ->searchable()
                            ->preload()
                            ->required(),

                        // Waktu
                        Forms\Components\DateTimePicker::make('departure_time')
                            ->label('Waktu Keberangkatan')
                            ->required(),

                        Forms\Components\DateTimePicker::make('arrival_time')
                            ->label('Waktu Tiba')
                            ->required(),

                        // Harga
                        Forms\Components\TextInput::make('price')
                            ->label('Harga Tiket')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('train.name')->label('Kereta'),
                Tables\Columns\TextColumn::make('originStation.name')->label('Asal'),
                Tables\Columns\TextColumn::make('destinationStation.name')->label('Tujuan'),
                Tables\Columns\TextColumn::make('departure_time')->label('Berangkat')->dateTime(),
                Tables\Columns\TextColumn::make('price')->label('Harga')->money('idr'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()->modalWidth('lg'), // Lebih lebar untuk jadwal
                    Tables\Actions\DeleteAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedules::route('/'),
        ];
    }
}
