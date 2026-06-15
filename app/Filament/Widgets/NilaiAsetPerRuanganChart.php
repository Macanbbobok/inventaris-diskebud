<?php

namespace App\Filament\Widgets;

use App\Models\Barang;
use Filament\Widgets\ChartWidget;

class NilaiAsetPerRuanganChart extends ChartWidget
{
    protected ?string $heading = 'Nilai Aset per Ruangan';

    protected ?string $description = '10 ruangan dengan nilai aset terbesar';

    protected ?string $pollingInterval = null;

    protected int | string | array $columnSpan = 1;

    protected ?string $maxHeight = '320px';

    protected function getData(): array
    {
        $rows = Barang::query()
            ->join('ruangan', 'barang.ruangan_id', '=', 'ruangan.id')
            ->where('barang.status', '!=', 'dihapus')
            ->selectRaw('ruangan.nama_ruangan as label, SUM(barang.harga_perolehan) as total')
            ->groupBy('ruangan.id', 'ruangan.nama_ruangan')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Nilai Aset',
                    'data' => $rows->pluck('total')->map(fn ($value): float => (float) $value)->all(),
                    'backgroundColor' => '#f59e0b',
                ],
            ],
            'labels' => $rows->pluck('label')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
