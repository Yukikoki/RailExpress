<?php

namespace App\Filament\Resources\CarriageResource\Pages;

use App\Filament\Resources\CarriageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCarriages extends ListRecords
{
    protected static string $resource = CarriageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make()
                ->label('Tambah Gerbong')
                ->modalWidth('md'),
        ];
    }
}
