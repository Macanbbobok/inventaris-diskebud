<?php

namespace App\Filament\Resources\MutasiBarangs\Widgets;

use App\Models\MutasiBarang;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MutasiBarangStatsOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth()->toDateString();
        $endOfMonth = $now->copy()->endOfMonth()->toDateString();
        $startOfYear = $now->copy()->startOfYear()->toDateString();
        $endOfYear = $now->copy()->endOfYear()->toDateString();

        $stats = MutasiBarang::query()
            ->selectRaw('
                COUNT(*) as total_mutasi,
                SUM(CASE WHEN tanggal_mutasi BETWEEN ? AND ? THEN 1 ELSE 0 END) as mutasi_bulan_ini,
                SUM(CASE WHEN tanggal_mutasi BETWEEN ? AND ? THEN 1 ELSE 0 END) as mutasi_tahun_ini,
                COUNT(DISTINCT barang_id) as barang_pernah_dimutasi
            ', [$startOfMonth, $endOfMonth, $startOfYear, $endOfYear])
            ->first();

        $totalMutasi = (int) ($stats->total_mutasi ?? 0);
        $mutasiBulanIni = (int) ($stats->mutasi_bulan_ini ?? 0);
        $mutasiTahunIni = (int) ($stats->mutasi_tahun_ini ?? 0);
        $barangPernahDimutasi = (int) ($stats->barang_pernah_dimutasi ?? 0);

        return [
            Stat::make('Total Mutasi', number_format($totalMutasi, 0, ',', '.')),

            Stat::make('Mutasi Bulan Ini', number_format($mutasiBulanIni, 0, ',', '.'))
                ->description(now()->translatedFormat('F Y'))
                ->color('info'),

            Stat::make('Mutasi Tahun Ini', number_format($mutasiTahunIni, 0, ',', '.'))
                ->description((string) now()->year),

            Stat::make('Barang Pernah Dimutasi', number_format($barangPernahDimutasi, 0, ',', '.')),
        ];
    }
}
