<?php

namespace App\Filament\Widgets;

use App\Models\MutasiBarang;
use App\Models\Pemeliharaan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TrenInventarisChart extends ChartWidget
{
    protected ?string $heading = 'Tren Mutasi & Biaya Pemeliharaan';

    protected ?string $description = 'Pergerakan 6 bulan terakhir';

    protected ?string $pollingInterval = null;

    protected int | string | array $columnSpan = 'full';

    protected ?string $maxHeight = '320px';

    protected function getData(): array
    {
        $months = collect(range(5, 0))
            ->map(fn (int $monthsAgo): Carbon => now()->startOfMonth()->subMonths($monthsAgo));
        $startDate = $months->first()->copy()->startOfMonth()->toDateString();
        $endDate = $months->last()->copy()->endOfMonth()->toDateString();
        $mutasiPerMonth = MutasiBarang::query()
            ->whereBetween('tanggal_mutasi', [$startDate, $endDate])
            ->selectRaw("DATE_FORMAT(tanggal_mutasi, '%Y-%m') as period, COUNT(*) as total")
            ->groupByRaw("DATE_FORMAT(tanggal_mutasi, '%Y-%m')")
            ->pluck('total', 'period');
        $biayaPerMonth = Pemeliharaan::query()
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as period, COALESCE(SUM(biaya), 0) as total")
            ->groupByRaw("DATE_FORMAT(tanggal, '%Y-%m')")
            ->pluck('total', 'period');

        return [
            'datasets' => [
                [
                    'label' => 'Mutasi',
                    'data' => $months
                        ->map(fn (Carbon $month): int => (int) ($mutasiPerMonth[$month->format('Y-m')] ?? 0))
                        ->all(),
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.2)',
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Biaya Pemeliharaan (Rp)',
                    'data' => $months
                        ->map(fn (Carbon $month): float => (float) ($biayaPerMonth[$month->format('Y-m')] ?? 0))
                        ->all(),
                    'borderColor' => '#dc2626',
                    'backgroundColor' => 'rgba(220, 38, 38, 0.2)',
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $months
                ->map(fn (Carbon $month): string => $month->translatedFormat('M Y'))
                ->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Jumlah Mutasi',
                    ],
                ],
                'y1' => [
                    'beginAtZero' => true,
                    'position' => 'right',
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Biaya (Rp)',
                    ],
                ],
            ],
        ];
    }
}
