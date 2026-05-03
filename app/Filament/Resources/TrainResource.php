<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrainResource\Pages;
use App\Filament\Resources\TrainResource\RelationManagers;
use App\Models\Train;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TrainResource extends Resource
{
    protected static ?string $model = Train::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Data Master Kereta';

    protected static ?int $navigationSort = 2; // Urutan menu

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Kereta')
                            ->required()
                            ->placeholder('Contoh: Argo Bromo Anggrek'),

                        Forms\Components\Select::make('class')
                            ->label('Kelas')
                            ->options([
                                'Eksekutif' => 'Eksekutif',
                                'Bisnis' => 'Bisnis',
                                'Ekonomi' => 'Ekonomi',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('total_seats')
                            ->label('Total Kursi')
                            ->numeric()
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama Kereta'),
                Tables\Columns\TextColumn::make('class')->label('Kelas')->badge(),
                Tables\Columns\TextColumn::make('total_seats')->label('Kapasitas'),
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
            'index' => Pages\ListTrains::route('/'),
        ];
    }
}
