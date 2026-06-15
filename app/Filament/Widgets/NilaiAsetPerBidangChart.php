<?php

namespace App\Filament\Widgets;

use App\Models\Barang;
use Filament\Widgets\ChartWidget;

class NilaiAsetPerBidangChart extends ChartWidget
{
    protected ?string $heading = 'Nilai Aset per Bidang';

    protected ?string $description = 'Bidang dengan akumulasi nilai aset terbesar';

    protected ?string $pollingInterval = null;

    protected int | string | array $columnSpan = 1;

    protected ?string $maxHeight = '320px';

    protected function getData(): array
    {
        $rows = Barang::query()
            ->leftJoin('bidang', 'barang.bidang_id', '=', 'bidang.id')
            ->where('barang.status', '!=', 'dihapus')
            ->selectRaw("COALESCE(bidang.nama_bidang, 'Belum Diisi') as label, SUM(barang.harga_perolehan) as total")
            ->groupByRaw("COALESCE(bidang.nama_bidang, 'Belum Diisi')")
            ->orderByDesc('total')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Nilai Aset',
                    'data' => $rows->pluck('total')->map(fn ($value): float => (float) $value)->all(),
                    'backgroundColor' => '#0f766e',
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
