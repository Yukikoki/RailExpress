<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarriageResource\Pages;
use App\Filament\Resources\CarriageResource\RelationManagers;
use App\Models\Carriage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CarriageResource extends Resource
{
    protected static ?string $model = Carriage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Data Master Kereta';

    protected static ?int $navigationSort = 3; // Urutan menu

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('train_id')
                    ->relationship('train', 'name')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->placeholder('Eks-1'),
                Forms\Components\Select::make('type')
                    ->options([
                        'Eksekutif' => 'Eksekutif',
                        'Ekonomi' => 'Ekonomi',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('train.name')->label('Kereta'),
                Tables\Columns\TextColumn::make('name')->label('Gerbong'),
                Tables\Columns\TextColumn::make('type')->label('Tipe'),
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
            'index' => Pages\ListCarriages::route('/'),
        ];
    }
}
