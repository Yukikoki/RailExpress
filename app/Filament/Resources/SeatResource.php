<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeatResource\Pages;
use App\Filament\Resources\SeatResource\RelationManagers;
use App\Models\Seat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SeatResource extends Resource
{
    protected static ?string $model = Seat::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Data Master Kereta';

    protected static ?int $navigationSort = 4; // Urutan menu

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        // Pilih Gerbong (Carriage)
                        Forms\Components\Select::make('carriage_id')
                            ->relationship('carriage', 'name')
                            ->label('Gerbong')
                            ->searchable()
                            ->preload()
                            ->required(),

                        // Nomor Kursi
                        Forms\Components\TextInput::make('seat_number')
                            ->label('Nomor Kursi')
                            ->placeholder('Contoh: 1A, 1B, 2A')
                            ->required(),

                        // Status Kursi (Aktif/Tidak)
                        Forms\Components\Toggle::make('is_active')
                            ->label('Kursi Dapat Digunakan')
                            ->default(true),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('carriage.train.name')->label('Kereta'),
                Tables\Columns\TextColumn::make('carriage.name')->label('Gerbong'),
                Tables\Columns\TextColumn::make('seat_number')->label('No. Kursi'),
                Tables\Columns\IconColumn::make('is_active')->label('Status')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->modalWidth('md'),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListSeats::route('/'),
        ];
    }
}
