<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Bidang;
use App\Models\KibBPeralatanMesin;
use App\Models\MutasiBarang;
use App\Models\Pemeliharaan;
use App\Models\Ruangan;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LaporanPeriodikTestingSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@diskebud.test'],
            [
                'name' => 'Admin Inventaris',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'bidang' => 'Sekretariat',
            ],
        );

        $sekretariat = Bidang::updateOrCreate([
            'nama_bidang' => 'Sekretariat',
        ]);

        $pelestarian = Bidang::updateOrCreate([
            'nama_bidang' => 'Bidang Pelestarian Adat dan Nilai Budaya',
        ]);

        $ruangArsip = Ruangan::updateOrCreate(
            ['kode_ruangan' => 'TST-R-ARSIP'],
            [
                'nama_ruangan' => 'Ruang Arsip Pengujian',
                'lantai' => '1',
            ],
        );

        $ruangAula = Ruangan::updateOrCreate(
            ['kode_ruangan' => 'TST-R-AULA'],
            [
                'nama_ruangan' => 'Aula Pengujian',
                'lantai' => '2',
            ],
        );

        $ruangArsip->bidangs()->syncWithoutDetaching([$sekretariat->id]);
        $ruangAula->bidangs()->syncWithoutDetaching([$pelestarian->id]);

        $items = [
            [
                'kode' => 'TST-2026-001',
                'nama' => 'Laptop Dokumentasi Budaya',
                'tanggal_barang' => '2026-01-10',
                'tanggal_mutasi' => '2026-01-20',
                'tanggal_pemeliharaan' => '2026-01-25',
                'harga' => 9800000,
                'merk' => 'Lenovo ThinkPad E14',
                'spesifikasi' => 'Intel Core i5, RAM 16GB, SSD 512GB',
                'bidang_id' => $sekretariat->id,
                'ruangan_asal_id' => $ruangArsip->id,
                'ruangan_tujuan_id' => $ruangAula->id,
            ],
            [
                'kode' => 'TST-2026-002',
                'nama' => 'Kamera Dokumentasi Kegiatan',
                'tanggal_barang' => '2026-03-12',
                'tanggal_mutasi' => '2026-03-22',
                'tanggal_pemeliharaan' => '2026-03-27',
                'harga' => 7250000,
                'merk' => 'Canon EOS M50',
                'spesifikasi' => 'Mirrorless, lensa kit 15-45mm',
                'bidang_id' => $pelestarian->id,
                'ruangan_asal_id' => $ruangAula->id,
                'ruangan_tujuan_id' => $ruangArsip->id,
            ],
            [
                'kode' => 'TST-2026-003',
                'nama' => 'Printer Layanan Arsip',
                'tanggal_barang' => '2026-05-08',
                'tanggal_mutasi' => '2026-05-18',
                'tanggal_pemeliharaan' => '2026-05-23',
                'harga' => 3100000,
                'merk' => 'Epson L5290',
                'spesifikasi' => 'Print, scan, copy, ADF',
                'bidang_id' => $sekretariat->id,
                'ruangan_asal_id' => $ruangArsip->id,
                'ruangan_tujuan_id' => $ruangAula->id,
            ],
            [
                'kode' => 'TST-2026-004',
                'nama' => 'Proyektor Edukasi Museum',
                'tanggal_barang' => '2026-07-09',
                'tanggal_mutasi' => '2026-07-19',
                'tanggal_pemeliharaan' => '2026-07-24',
                'harga' => 8500000,
                'merk' => 'Epson EB-E500',
                'spesifikasi' => 'XGA, 3600 lumens',
                'bidang_id' => $pelestarian->id,
                'ruangan_asal_id' => $ruangAula->id,
                'ruangan_tujuan_id' => $ruangArsip->id,
            ],
            [
                'kode' => 'TST-2026-005',
                'nama' => 'Scanner Arsip Naskah',
                'tanggal_barang' => '2026-09-14',
                'tanggal_mutasi' => '2026-09-21',
                'tanggal_pemeliharaan' => '2026-09-26',
                'harga' => 4600000,
                'merk' => 'Canon LiDE 400',
                'spesifikasi' => 'Flatbed scanner A4',
                'bidang_id' => $sekretariat->id,
                'ruangan_asal_id' => $ruangArsip->id,
                'ruangan_tujuan_id' => $ruangAula->id,
            ],
            [
                'kode' => 'TST-2026-006',
                'nama' => 'Speaker Aula Pertunjukan',
                'tanggal_barang' => '2026-12-05',
                'tanggal_mutasi' => '2026-12-16',
                'tanggal_pemeliharaan' => '2026-12-21',
                'harga' => 5700000,
                'merk' => 'Yamaha DBR10',
                'spesifikasi' => 'Active speaker 10 inch',
                'bidang_id' => $pelestarian->id,
                'ruangan_asal_id' => $ruangAula->id,
                'ruangan_tujuan_id' => $ruangArsip->id,
            ],
        ];

        foreach ($items as $item) {
            $barang = Barang::updateOrCreate(
                ['kode_barang' => $item['kode']],
                [
                    'nama_barang' => $item['nama'],
                    'bidang_id' => $item['bidang_id'],
                    'ruangan_id' => $item['ruangan_asal_id'],
                    'tahun_perolehan' => 2026,
                    'tanggal_perolehan' => $item['tanggal_barang'],
                    'kondisi' => 'baik',
                    'status' => 'aktif',
                    'harga_perolehan' => $item['harga'],
                    'created_by' => $admin->id,
                ],
            );

            $this->setTimestamps($barang, $item['tanggal_barang']);

            $kibB = KibBPeralatanMesin::updateOrCreate(
                ['barang_id' => $barang->id],
                [
                    'merk_type' => $item['merk'],
                    'ukuran' => '-',
                    'bahan' => 'Campuran',
                    'no_seri' => $item['kode'].'-SN',
                    'spesifikasi' => $item['spesifikasi'],
                ],
            );

            $this->setTimestamps($kibB, $item['tanggal_barang']);

            MutasiBarang::updateOrCreate(
                [
                    'barang_id' => $barang->id,
                    'tanggal_mutasi' => $item['tanggal_mutasi'],
                ],
                [
                    'ruangan_asal_id' => $item['ruangan_asal_id'],
                    'ruangan_tujuan_id' => $item['ruangan_tujuan_id'],
                    'alasan' => 'Data simulasi pengujian laporan periodik.',
                    'kondisi_sebelum' => 'baik',
                    'kondisi_sesudah' => 'baik',
                    'dilakukan_oleh' => $admin->id,
                ],
            );

            Pemeliharaan::updateOrCreate(
                [
                    'barang_id' => $barang->id,
                    'tanggal' => $item['tanggal_pemeliharaan'],
                    'jenis_pemeliharaan' => 'Pemeriksaan berkala',
                ],
                [
                    'request_kerusakan_id' => null,
                    'deskripsi' => 'Data simulasi pemeriksaan berkala untuk pengujian laporan periodik.',
                    'biaya' => 150000,
                    'kondisi_sesudah' => 'baik',
                    'dilakukan_oleh' => $admin->id,
                ],
            );
        }
    }

    private function setTimestamps(Model $model, string $date): void
    {
        $timestamp = CarbonImmutable::parse($date)->setTime(9, 0);

        $model->forceFill([
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ])->saveQuietly();
    }
}
