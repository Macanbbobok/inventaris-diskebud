<?php

use App\Http\Controllers\LaporanBarangController;
use App\Models\Bidang;
use App\Models\Barang;
use App\Models\RequestKerusakan;
use App\Models\Ruangan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::get('/', function () {
    if (! Schema::hasTable('barang')
        || ! Schema::hasTable('ruangan')
        || ! Schema::hasTable('bidang')
        || ! Schema::hasTable('request_kerusakan')) {
        return view('welcome');
    }

    $formatNumber = fn (int|float $value): string => number_format($value, 0, ',', '.');

    $formatRupiah = function (int|float $value): string {
        if ($value >= 1_000_000_000) {
            return 'Rp '.number_format($value / 1_000_000_000, 1, ',', '.').' M';
        }

        if ($value >= 1_000_000) {
            return 'Rp '.number_format($value / 1_000_000, 1, ',', '.').' jt';
        }

        if ($value >= 1_000) {
            return 'Rp '.number_format($value / 1_000, 0, ',', '.').' rb';
        }

        return 'Rp '.number_format($value, 0, ',', '.');
    };

    $totalBarang = Barang::query()->count();
    $barangAktif = Barang::query()->where('status', 'aktif')->count();
    $totalRuangan = Ruangan::query()->count();
    $totalBidang = Bidang::query()->count();
    $requestAktif = RequestKerusakan::query()
        ->whereIn('status', ['diajukan', 'diproses'])
        ->count();

    $conditionTotal = max($totalBarang, 1);
    $conditionStats = collect([
        ['label' => 'Baik', 'value' => Barang::query()->where('kondisi', 'baik')->count(), 'class' => 'is-good'],
        ['label' => 'Rusak ringan', 'value' => Barang::query()->where('kondisi', 'rusak_ringan')->count(), 'class' => 'is-warning'],
        ['label' => 'Rusak berat', 'value' => Barang::query()->where('kondisi', 'rusak_berat')->count(), 'class' => 'is-danger'],
    ])->map(fn (array $condition): array => [
        ...$condition,
        'percent' => (int) round(($condition['value'] / $conditionTotal) * 100),
    ])->all();

    return view('welcome', [
        'stats' => [
            [
                'label' => 'Total Aset',
                'value' => $formatNumber($totalBarang),
                'caption' => $formatNumber($barangAktif).' aset aktif',
            ],
            [
                'label' => 'Ruangan',
                'value' => $formatNumber($totalRuangan),
                'caption' => $formatNumber($totalBidang).' bidang terhubung',
            ],
            [
                'label' => 'Laporan Kerusakan',
                'value' => $formatNumber($requestAktif),
                'caption' => 'Perlu tindak lanjut',
            ],
            [
                'label' => 'Nilai Perolehan',
                'value' => $formatRupiah((float) Barang::query()->sum('harga_perolehan')),
                'caption' => 'Akumulasi aset',
            ],
        ],
        'conditionStats' => $conditionStats,
    ]);
});

Route::get('/barang/{barang}', function (Barang $barang) {
    return view('barang-show', compact('barang'));
})->name('barang.public.show');

Route::get('/laporan/barang/pdf', [LaporanBarangController::class, 'barang'])
    ->name('laporan.barang.pdf');
