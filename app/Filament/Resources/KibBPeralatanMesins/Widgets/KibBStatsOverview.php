<?php

namespace App\Filament\Resources\KibBPeralatanMesins\Widgets;

use App\Models\Barang;
use App\Models\KibBPeralatanMesin;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class KibBStatsOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $totalBarang = Barang::count();
        $totalKibB = KibBPeralatanMesin::count();
        $belumLengkap = Barang::query()
            ->whereDoesntHave('detailKibB')
            ->count();

        return [
            Stat::make('Total Detail KIB B', number_format($totalKibB, 0, ',', '.')),

            Stat::make('Belum Lengkap KIB B', number_format($belumLengkap, 0, ',', '.'))
                ->description($this->formatPercentage($belumLengkap, $totalBarang) . ' dari total barang')
                ->color($belumLengkap > 0 ? 'warning' : 'success'),

            Stat::make('Total Barang', number_format($totalBarang, 0, ',', '.')),
        ];
    }

    private function formatPercentage(int $value, int $total): string
    {
        if ($total === 0) {
            return '0%';
        }

        return number_format(($value / $total) * 100, 1, ',', '.') . '%';
    }
}
