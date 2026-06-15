<?php

namespace App\Filament\Resources\ImportLogs\Widgets;

use App\Models\ImportLog;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ImportLogStatsOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $stats = ImportLog::query()
            ->selectRaw('
                COUNT(*) as total_import,
                COALESCE(SUM(berhasil), 0) as total_berhasil,
                COALESCE(SUM(gagal), 0) as total_gagal,
                COALESCE(SUM(duplikat), 0) as total_duplikat
            ')
            ->first();
        $importTerakhir = ImportLog::latest('created_at')
            ->first(['id', 'nama_file', 'status', 'berhasil', 'gagal', 'duplikat']);

        $totalImport = (int) ($stats->total_import ?? 0);
        $totalBerhasil = (int) ($stats->total_berhasil ?? 0);
        $totalGagal = (int) ($stats->total_gagal ?? 0);
        $totalDuplikat = (int) ($stats->total_duplikat ?? 0);

        return [
            Stat::make('Total Import', number_format($totalImport, 0, ',', '.')),

            Stat::make('Total Berhasil', number_format($totalBerhasil, 0, ',', '.'))
                ->color('success'),

            Stat::make('Total Gagal', number_format($totalGagal, 0, ',', '.'))
                ->color($totalGagal > 0 ? 'danger' : 'success'),

            Stat::make('Total Duplikat', number_format($totalDuplikat, 0, ',', '.'))
                ->color($totalDuplikat > 0 ? 'warning' : 'success'),

            Stat::make(
                'Import Terakhir',
                $importTerakhir ? $this->formatImportLabel($importTerakhir) : '-'
            )
                ->description($importTerakhir?->nama_file ?? 'Belum ada riwayat import')
                ->color($importTerakhir ? $this->formatImportColor($importTerakhir) : 'gray'),
        ];
    }

    private function formatImportLabel(ImportLog $importLog): string
    {
        if (
            $importLog->berhasil === 0
            && $importLog->gagal === 0
            && $importLog->duplikat > 0
        ) {
            return 'Duplikat';
        }

        return match ($importLog->status) {
            'sukses' => 'Sukses',
            'sebagian' => 'Sebagian',
            'gagal' => 'Gagal',
            default => '-',
        };
    }

    private function formatImportColor(ImportLog $importLog): string
    {
        if (
            $importLog->berhasil === 0
            && $importLog->gagal === 0
            && $importLog->duplikat > 0
        ) {
            return 'warning';
        }

        return match ($importLog->status) {
            'sukses' => 'success',
            'sebagian' => 'warning',
            'gagal' => 'danger',
            default => 'gray',
        };
    }
}
