<?php

namespace App\Filament\Resources\Pemeliharaans\Widgets;

use App\Models\Barang;
use App\Models\Pemeliharaan;
use App\Models\RequestKerusakan;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PemeliharaanStatsOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth()->toDateString();
        $endOfMonth = $now->copy()->endOfMonth()->toDateString();

        $pemeliharaanStats = Pemeliharaan::query()
            ->selectRaw('
                COUNT(*) as total_pemeliharaan,
                SUM(CASE WHEN tanggal BETWEEN ? AND ? THEN 1 ELSE 0 END) as pemeliharaan_bulan_ini,
                COALESCE(SUM(CASE WHEN tanggal BETWEEN ? AND ? THEN biaya ELSE 0 END), 0) as biaya_bulan_ini
            ', [$startOfMonth, $endOfMonth, $startOfMonth, $endOfMonth])
            ->first();
        $requestBelumSelesai = RequestKerusakan::query()
            ->whereIn('status', ['diajukan', 'diproses'])
            ->count();
        $barangRusakBelumDipelihara = Barang::query()
            ->whereIn('kondisi', ['rusak_ringan', 'rusak_berat'])
            ->whereDoesntHave('pemeliharaan')
            ->count();

        $totalPemeliharaan = (int) ($pemeliharaanStats->total_pemeliharaan ?? 0);
        $pemeliharaanBulanIni = (int) ($pemeliharaanStats->pemeliharaan_bulan_ini ?? 0);
        $biayaBulanIni = (float) ($pemeliharaanStats->biaya_bulan_ini ?? 0);

        return [
            Stat::make('Total Pemeliharaan', number_format($totalPemeliharaan, 0, ',', '.')),

            Stat::make('Pemeliharaan Bulan Ini', number_format($pemeliharaanBulanIni, 0, ',', '.'))
                ->description(now()->translatedFormat('F Y'))
                ->color('warning'),

            Stat::make(
                'Biaya Bulan Ini',
                'Rp ' . number_format($biayaBulanIni, 0, ',', '.')
            )
                ->description(now()->translatedFormat('F Y'))
                ->color('danger'),

            Stat::make('Request Belum Selesai', number_format($requestBelumSelesai, 0, ',', '.'))
                ->description('Status diajukan atau diproses')
                ->color($requestBelumSelesai > 0 ? 'warning' : 'success'),

            Stat::make('Rusak Belum Dipelihara', number_format($barangRusakBelumDipelihara, 0, ',', '.'))
                ->description('Barang rusak tanpa riwayat pemeliharaan')
                ->color($barangRusakBelumDipelihara > 0 ? 'danger' : 'success'),
        ];
    }
}
