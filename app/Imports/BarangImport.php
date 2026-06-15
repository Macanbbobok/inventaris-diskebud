<?php

namespace App\Imports;

use App\Models\Barang;
use App\Models\Bidang;
use App\Models\ImportLog;
use App\Models\KibBPeralatanMesin;
use App\Models\Ruangan;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class BarangImport implements ToCollection, WithHeadingRow
{
    public int $totalBaris = 0;

    public int $berhasil = 0;

    public int $gagal = 0;

    public int $duplikat = 0;

    public array $errors = [];

    public array $detailErrors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            $this->totalBaris++;

            try {
                $kodeBarang = trim((string) ($row['kode_barang'] ?? ''));

                if (
                    $kodeBarang !== ''
                    && Barang::withTrashed()
                        ->where('kode_barang', $kodeBarang)
                        ->exists()
                ) {
                    $this->duplikat++;

                    $this->detailErrors[] = [
                        'baris' => $this->totalBaris,
                        'tipe' => 'duplikat',
                        'pesan' => "Kode barang {$kodeBarang} sudah ada.",
                        'data' => is_array($row) ? $row : $row->toArray(),
                    ];

                    continue;
                }

                $namaRuangan = trim((string) ($row['ruangan'] ?? ''));
                $namaBidang = trim((string) ($row['bidang'] ?? ''));
                $kodeRuangan = trim((string) ($row['kode_ruangan'] ?? ''));

                if ($namaRuangan === '') {
                    throw new \InvalidArgumentException('Kolom ruangan wajib diisi.');
                }

                if ($namaBidang === '') {
                    throw new \InvalidArgumentException('Kolom bidang wajib diisi.');
                }

                $ruangan = Ruangan::firstOrCreate(
                    [
                        'nama_ruangan' => $namaRuangan,
                    ],
                    [
                        'kode_ruangan' => $kodeRuangan !== ''
                            ? $kodeRuangan
                            : $this->buatKodeRuanganOtomatis(),
                    ]
                );

                $bidang = Bidang::firstOrCreate([
                    'nama_bidang' => $namaBidang,
                ]);

                $ruangan->bidangs()->syncWithoutDetaching([$bidang->id]);

                $barang = Barang::create([
                    'kode_barang' => $kodeBarang,
                    'nama_barang' => $row['nama_barang'],
                    'bidang_id' => $bidang->id,
                    'ruangan_id' => $ruangan->id,
                    'tahun_perolehan' => $row['tahun_perolehan'],
                    'tanggal_perolehan' => $this->tanggalPerolehan($row),
                    'kondisi' => $row['kondisi'],
                    'status' => $row['status'],
                    'harga_perolehan' => $row['harga_perolehan'],
                    'created_by' => Auth::id(),
                ]);

                if ($this->memilikiDetailKibB($row)) {
                    KibBPeralatanMesin::create([
                        'barang_id' => $barang->id,
                        'merk_type' => $row['merk_type'] ?? null,
                        'ukuran' => $row['ukuran'] ?? null,
                        'bahan' => $row['bahan'] ?? null,
                        'no_seri' => $row['no_seri'] ?? null,
                        'spesifikasi' => $row['spesifikasi'] ?? null,
                    ]);
                }

                $this->berhasil++;

            } catch (\Throwable $e) {

                $this->gagal++;

                $this->errors[] =
                    'Baris '.
                    $this->totalBaris.
                    ': '.
                    $e->getMessage();

                $this->detailErrors[] = [
                    'baris' => $this->totalBaris,
                    'tipe' => 'gagal',
                    'pesan' => $e->getMessage(),
                    'data' => is_array($row) ? $row : $row->toArray(),
                ];
            }
        }
    }

    public function simpanLog(
        string $namaFile,
        ?string $pathFile = null,
    ): void {
        $catatan = [];

        if ($this->duplikat > 0) {
            $catatan[] = "{$this->duplikat} baris dilewati karena kode barang duplikat.";
        }

        if (! empty($this->errors)) {
            $catatan = array_merge($catatan, $this->errors);
        }

        ImportLog::create([
            'nama_file' => $namaFile,

            'tipe_import' => 'excel_barang',

            'jenis_kib' => 'B',

            'total_baris' => $this->totalBaris,

            'berhasil' => $this->berhasil,

            'gagal' => $this->gagal,

            'duplikat' => $this->duplikat,

            'status' => match (true) {

                $this->gagal === 0 && $this->duplikat === 0 => 'sukses',

                $this->berhasil > 0 => 'sebagian',

                default => 'gagal',
            },

            'catatan_error' => empty($catatan)
                ? null
                : implode("\n", $catatan),

            'detail_error' => empty($this->detailErrors)
                ? null
                : json_encode(
                    $this->detailErrors,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                ),

            'path_file' => $pathFile,

            'waktu_selesai' => now(),

            'diupload_oleh' => Auth::id(),
        ]);
    }

    private function buatKodeRuanganOtomatis(): string
    {
        do {
            $kode = 'AUTO-'.random_int(100, 999);
        } while (Ruangan::withTrashed()->where('kode_ruangan', $kode)->exists());

        return $kode;
    }

    private function memilikiDetailKibB(Collection $row): bool
    {
        return collect([
            $row['merk_type'] ?? null,
            $row['ukuran'] ?? null,
            $row['bahan'] ?? null,
            $row['no_seri'] ?? null,
            $row['spesifikasi'] ?? null,
        ])->contains(fn ($value): bool => filled($value));
    }

    private function tanggalPerolehan(Collection $row): ?string
    {
        $tanggal = $row['tanggal_perolehan'] ?? null;

        if (filled($tanggal)) {
            if (is_numeric($tanggal)) {
                return CarbonImmutable::instance(ExcelDate::excelToDateTimeObject((float) $tanggal))
                    ->toDateString();
            }

            return CarbonImmutable::parse($tanggal)->toDateString();
        }

        $tahun = $row['tahun_perolehan'] ?? null;

        if (blank($tahun)) {
            return null;
        }

        return sprintf('%04d-01-01', (int) $tahun);
    }
}
