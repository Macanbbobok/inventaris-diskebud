<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\KibBPeralatanMesin;
use App\Models\LaporanInventaris;
use App\Models\MutasiBarang;
use App\Models\Pemeliharaan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class LaporanInventarisGenerator
{
    private const JENIS_LAPORAN = [
        'barang',
        'kib_b',
        'mutasi',
        'pemeliharaan',
        'gabungan',
    ];

    private const PERIODE = [
        'bulanan',
        'triwulanan',
        'semesteran',
        'tahunan',
    ];

    public function generate(array $data): LaporanInventaris
    {
        $data = $this->normalizeData($data);

        [$startMonth, $endMonth] = $this->monthRange(
            $data['periode'],
            $data['bulan'],
        );

        $barangs = Barang::with('bidang', 'ruangan', 'detailKibB')
            ->whereYear('tanggal_perolehan', $data['tahun'])
            ->whereMonth('tanggal_perolehan', '>=', $startMonth)
            ->whereMonth('tanggal_perolehan', '<=', $endMonth)
            ->get();

        $mutasi = MutasiBarang::with(
            'barang',
            'ruanganAsal',
            'ruanganTujuan',
            'petugas',
        )
            ->whereYear('tanggal_mutasi', $data['tahun'])
            ->whereMonth('tanggal_mutasi', '>=', $startMonth)
            ->whereMonth('tanggal_mutasi', '<=', $endMonth)
            ->get();

        $kibBPeralatanMesin = KibBPeralatanMesin::with('barang.bidang', 'barang.ruangan')
            ->whereHas('barang', fn ($query) => $query
                ->whereYear('tanggal_perolehan', $data['tahun'])
                ->whereMonth('tanggal_perolehan', '>=', $startMonth)
                ->whereMonth('tanggal_perolehan', '<=', $endMonth))
            ->get();

        $pemeliharaan = Pemeliharaan::with('barang', 'petugas')
            ->whereYear('tanggal', $data['tahun'])
            ->whereMonth('tanggal', '>=', $startMonth)
            ->whereMonth('tanggal', '<=', $endMonth)
            ->get();

        $pdf = Pdf::loadView('laporan.inventaris', [
            'data' => $data,
            'barangs' => $barangs,
            'kibBPeralatanMesin' => $kibBPeralatanMesin,
            'mutasi' => $mutasi,
            'pemeliharaan' => $pemeliharaan,
        ])->setPaper('a4', 'landscape');

        $fileName = sprintf(
            'laporan/laporan-%s-%s-%s-%s.pdf',
            $data['jenis_laporan'],
            $data['periode'],
            $this->periodFileSuffix($data),
            now()->format('Ymd-His'),
        );

        Storage::disk('public')->put($fileName, $pdf->output());

        $data['file_laporan'] = $fileName;

        return LaporanInventaris::create($data);
    }

    public function monthRange(string $periode, ?int $bulan): array
    {
        if ($periode === 'tahunan') {
            return [1, 12];
        }

        if ($bulan === null || $bulan < 1 || $bulan > 12) {
            throw new InvalidArgumentException('Bulan laporan harus berada pada rentang 1 sampai 12.');
        }

        $startMonth = match ($periode) {
            'bulanan' => $bulan,
            'triwulanan' => (int) ((ceil($bulan / 3) - 1) * 3 + 1),
            'semesteran' => $bulan <= 6 ? 1 : 7,
            default => throw new InvalidArgumentException('Periode laporan tidak valid.'),
        };

        $endMonth = match ($periode) {
            'bulanan' => $bulan,
            'triwulanan' => $startMonth + 2,
            'semesteran' => $startMonth + 5,
            default => throw new InvalidArgumentException('Periode laporan tidak valid.'),
        };

        return [$startMonth, $endMonth];
    }

    private function normalizeData(array $data): array
    {
        $jenisLaporan = $data['jenis_laporan'] ?? null;
        $periode = $data['periode'] ?? null;

        if (! in_array($jenisLaporan, self::JENIS_LAPORAN, true)) {
            throw new InvalidArgumentException('Jenis laporan tidak valid.');
        }

        if (! in_array($periode, self::PERIODE, true)) {
            throw new InvalidArgumentException('Periode laporan tidak valid.');
        }

        if (blank($data['tahun'] ?? null)) {
            throw new InvalidArgumentException('Tahun laporan wajib diisi.');
        }

        $data['tahun'] = (int) $data['tahun'];

        if ($periode === 'tahunan') {
            $data['bulan'] = null;
        } else {
            $data['bulan'] = (int) ($data['bulan'] ?? now()->month);
        }

        $data['dibuat_oleh'] = $data['dibuat_oleh'] ?? Auth::id();

        return $data;
    }

    private function periodFileSuffix(array $data): string
    {
        if ($data['periode'] === 'tahunan') {
            return (string) $data['tahun'];
        }

        return sprintf('%04d-%02d', $data['tahun'], $data['bulan']);
    }
}
