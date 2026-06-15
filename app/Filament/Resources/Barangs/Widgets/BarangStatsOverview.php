<?php

namespace App\Filament\Resources\Barangs\Widgets;

use App\Models\Barang;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BarangStatsOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $stats = Barang::withTrashed()
            ->selectRaw("
                COUNT(CASE WHEN deleted_at IS NULL THEN 1 END) as total_barang,
                SUM(CASE WHEN deleted_at IS NULL AND status = 'aktif' THEN 1 ELSE 0 END) as barang_aktif,
                SUM(CASE WHEN status = 'dihapus' OR deleted_at IS NOT NULL THEN 1 ELSE 0 END) as barang_dihapus,
                SUM(CASE WHEN deleted_at IS NULL AND kondisi = 'baik' THEN 1 ELSE 0 END) as barang_baik,
                SUM(CASE WHEN deleted_at IS NULL AND kondisi = 'rusak_ringan' THEN 1 ELSE 0 END) as rusak_ringan,
                SUM(CASE WHEN deleted_at IS NULL AND kondisi = 'rusak_berat' THEN 1 ELSE 0 END) as rusak_berat,
                COALESCE(SUM(CASE WHEN deleted_at IS NULL THEN harga_perolehan ELSE 0 END), 0) as total_nilai_aset,
                COALESCE(AVG(CASE WHEN deleted_at IS NULL THEN harga_perolehan END), 0) as rata_rata_harga
            ")
            ->first();

        $totalBarang = (int) ($stats->total_barang ?? 0);
        $barangAktif = (int) ($stats->barang_aktif ?? 0);
        $barangDihapus = (int) ($stats->barang_dihapus ?? 0);
        $barangBaik = (int) ($stats->barang_baik ?? 0);
        $rusakRingan = (int) ($stats->rusak_ringan ?? 0);
        $rusakBerat = (int) ($stats->rusak_berat ?? 0);
        $totalNilaiAset = (float) ($stats->total_nilai_aset ?? 0);
        $rataRataHarga = (float) ($stats->rata_rata_harga ?? 0);

        return [
            Stat::make('Total Barang', number_format($totalBarang, 0, ',', '.')),

            Stat::make('Barang Aktif', number_format($barangAktif, 0, ',', '.'))
                ->color('success'),

            Stat::make('Barang Dihapus', number_format($barangDihapus, 0, ',', '.'))
                ->color('danger'),

            Stat::make('Barang Baik', number_format($barangBaik, 0, ',', '.'))
                ->description($this->formatPercentage($barangBaik, $totalBarang) . ' dari total barang')
                ->color('success'),

            Stat::make('Rusak Ringan', number_format($rusakRingan, 0, ',', '.'))
                ->description($this->formatPercentage($rusakRingan, $totalBarang) . ' dari total barang')
                ->color('warning'),

            Stat::make('Rusak Berat', number_format($rusakBerat, 0, ',', '.'))
                ->description($this->formatPercentage($rusakBerat, $totalBarang) . ' dari total barang')
                ->color('danger'),

            Stat::make(
                'Total Nilai Aset',
                'Rp ' . number_format($totalNilaiAset, 0, ',', '.')
            ),

            Stat::make(
                'Rata-rata Harga',
                'Rp ' . number_format($rataRataHarga, 0, ',', '.')
            ),
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
