<?php

namespace App\Filament\Widgets;

use App\Models\Seat;
use App\Models\Train;
use App\Models\Schedule;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Armada', Train::count() . ' Kereta')
                ->description('Rangkaian kereta aktif')
                ->descriptionIcon('heroicon-m-truck')
                ->color('info'),

            Stat::make('Total Kapasitas', Seat::count() . ' Kursi')
                ->description('Total kursi tersedia')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Jadwal Aktif', Schedule::count() . ' Perjalanan')
                ->description('Total jadwal keberangkatan')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success'),
        ];
    }
}
