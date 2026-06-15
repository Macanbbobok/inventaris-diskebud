<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Bidang;
use App\Models\KibBPeralatanMesin;
use App\Models\Pemeliharaan;
use App\Models\RequestKerusakan;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InventarisDevelopmentSeeder extends Seeder
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

        User::updateOrCreate(
            ['email' => 'pimpinan@diskebud.test'],
            [
                'name' => 'Pimpinan',
                'password' => Hash::make('password'),
                'role' => 'pimpinan',
                'bidang' => 'Sekretariat',
            ],
        );

        User::updateOrCreate(
            ['email' => 'staff@diskebud.test'],
            [
                'name' => 'Staff Inventaris',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'bidang' => 'Sekretariat',
            ],
        );

        $sekretariat = Bidang::updateOrCreate([
            'nama_bidang' => 'Sekretariat',
        ]);

        $pelestarian = Bidang::updateOrCreate([
            'nama_bidang' => 'Bidang Pelestarian Adat dan Nilai Budaya',
        ]);

        $ruangSekretariat = Ruangan::updateOrCreate(
            ['kode_ruangan' => 'R-001'],
            [
                'nama_ruangan' => 'Ruang Sekretariat',
                'lantai' => '1',
            ],
        );

        $ruangRapat = Ruangan::updateOrCreate(
            ['kode_ruangan' => 'R-002'],
            [
                'nama_ruangan' => 'Ruang Rapat',
                'lantai' => '1',
            ],
        );

        $ruangPelestarian = Ruangan::updateOrCreate(
            ['kode_ruangan' => 'R-003'],
            [
                'nama_ruangan' => 'Ruang Pelestarian',
                'lantai' => '2',
            ],
        );

        $ruangSekretariat->bidangs()->syncWithoutDetaching([$sekretariat->id]);
        $ruangRapat->bidangs()->syncWithoutDetaching([$sekretariat->id, $pelestarian->id]);
        $ruangPelestarian->bidangs()->syncWithoutDetaching([$pelestarian->id]);

        $laptop = $this->barang(
            kodeBarang: 'BRG-0001',
            namaBarang: 'Laptop Inventaris',
            bidangId: $sekretariat->id,
            ruanganId: $ruangSekretariat->id,
            tahunPerolehan: 2024,
            kondisi: 'baik',
            hargaPerolehan: 9500000,
            createdBy: $admin->id,
        );

        $printer = $this->barang(
            kodeBarang: 'BRG-0002',
            namaBarang: 'Printer Administrasi',
            bidangId: $sekretariat->id,
            ruanganId: $ruangSekretariat->id,
            tahunPerolehan: 2023,
            kondisi: 'rusak_ringan',
            hargaPerolehan: 3200000,
            createdBy: $admin->id,
        );

        $proyektor = $this->barang(
            kodeBarang: 'BRG-0003',
            namaBarang: 'Proyektor Ruang Rapat',
            bidangId: $sekretariat->id,
            ruanganId: $ruangRapat->id,
            tahunPerolehan: 2022,
            kondisi: 'baik',
            hargaPerolehan: 7800000,
            createdBy: $admin->id,
        );

        $meja = $this->barang(
            kodeBarang: 'BRG-0004',
            namaBarang: 'Meja Kerja Staff',
            bidangId: $pelestarian->id,
            ruanganId: $ruangPelestarian->id,
            tahunPerolehan: 2021,
            kondisi: 'baik',
            hargaPerolehan: 1750000,
            createdBy: $admin->id,
        );

        $this->kibB($laptop, [
            'merk_type' => 'ASUS VivoBook 14',
            'ukuran' => '14 inch',
            'bahan' => 'Plastik dan aluminium',
            'no_seri' => 'ASUS-0001',
            'spesifikasi' => 'Intel Core i5, RAM 8GB, SSD 512GB',
        ]);

        $this->kibB($printer, [
            'merk_type' => 'Epson L3210',
            'ukuran' => 'A4',
            'bahan' => 'Plastik',
            'no_seri' => 'EPS-0002',
            'spesifikasi' => 'Print, scan, copy',
        ]);

        $this->kibB($proyektor, [
            'merk_type' => 'Epson EB-X500',
            'ukuran' => 'XGA',
            'bahan' => 'Plastik',
            'no_seri' => 'PRJ-0003',
            'spesifikasi' => '3600 lumens',
        ]);

        $this->kibB($meja, [
            'merk_type' => 'Meja Kantor Kayu',
            'ukuran' => '120 x 60 cm',
            'bahan' => 'Kayu',
            'no_seri' => null,
            'spesifikasi' => 'Meja kerja satu biro',
        ]);

        $requestPrinter = RequestKerusakan::updateOrCreate(
            [
                'barang_id' => $printer->id,
                'tanggal_laporan' => '2026-05-19',
                'deskripsi_kerusakan' => 'Hasil cetak putus-putus dan tinta tidak keluar normal.',
            ],
            [
                'tingkat_kerusakan' => 'ringan',
                'status' => 'diproses',
                'dilaporkan_oleh' => $admin->id,
            ],
        );

        Pemeliharaan::updateOrCreate(
            [
                'barang_id' => $printer->id,
                'request_kerusakan_id' => $requestPrinter->id,
                'tanggal' => '2026-05-20',
                'jenis_pemeliharaan' => 'Perbaikan head printer',
            ],
            [
                'deskripsi' => 'Pembersihan head dan penggantian tinta.',
                'biaya' => 250000,
                'kondisi_sesudah' => 'baik',
                'dilakukan_oleh' => $admin->id,
            ],
        );
    }

    private function barang(
        string $kodeBarang,
        string $namaBarang,
        int $bidangId,
        int $ruanganId,
        int $tahunPerolehan,
        string $kondisi,
        int $hargaPerolehan,
        int $createdBy,
    ): Barang {
        return Barang::updateOrCreate(
            ['kode_barang' => $kodeBarang],
            [
                'nama_barang' => $namaBarang,
                'bidang_id' => $bidangId,
                'ruangan_id' => $ruanganId,
                'tahun_perolehan' => $tahunPerolehan,
                'tanggal_perolehan' => sprintf('%04d-01-01', $tahunPerolehan),
                'kondisi' => $kondisi,
                'status' => 'aktif',
                'harga_perolehan' => $hargaPerolehan,
                'created_by' => $createdBy,
            ],
        );
    }

    private function kibB(Barang $barang, array $data): void
    {
        KibBPeralatanMesin::updateOrCreate(
            ['barang_id' => $barang->id],
            $data + ['barang_id' => $barang->id],
        );
    }
}
