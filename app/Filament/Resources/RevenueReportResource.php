<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RevenueReportResource\Pages;
use App\Filament\Resources\RevenueReportResource\RelationManagers;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RevenueReportResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $pluralModelLabel = 'Laporan Pendapatan';

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationLabel = 'Laporan Pendapatan';

    protected static ?string $slug = 'laporan-pendapatan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Transaksi')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('passengers.name')
                    ->label('Nama Penumpang')
                    ->listWithLineBreaks()
                    ->searchable(),

                Tables\Columns\TextColumn::make('schedule.train.name')
                    ->label('Kereta'),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total Bayar')
                    ->money('IDR')
                    ->summarize(Tables\Columns\Summarizers\Sum::make()
                    ->label('Total Pendapatan')
                    ->money('IDR')),
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('created_at', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('created_at', '<=', $data['until']));
                    })
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function canCreate(): bool
    {
        return false;
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
            'index' => Pages\ListRevenueReports::route('/'),
        ];
    }
}
