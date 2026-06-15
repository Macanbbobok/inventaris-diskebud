<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Bidang;
use App\Models\Pemeliharaan;
use App\Models\Ruangan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PemeliharaanTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_pemeliharaan_updates_barang_condition(): void
    {
        $bidang = Bidang::create([
            'nama_bidang' => 'Sekretariat',
        ]);

        $ruangan = Ruangan::create([
            'kode_ruangan' => 'R-001',
            'nama_ruangan' => 'Ruang Sekretariat',
        ]);

        $barang = Barang::withoutEvents(fn (): Barang => Barang::create([
            'kode_barang' => 'BRG-0001',
            'nama_barang' => 'Laptop Inventaris',
            'bidang_id' => $bidang->id,
            'ruangan_id' => $ruangan->id,
            'kondisi' => 'rusak_ringan',
            'status' => 'aktif',
            'harga_perolehan' => 5000000,
        ]));

        Pemeliharaan::create([
            'barang_id' => $barang->id,
            'tanggal' => '2026-05-20',
            'jenis_pemeliharaan' => 'Perbaikan',
            'biaya' => 250000,
            'kondisi_sesudah' => 'baik',
        ]);

        $this->assertSame('baik', $barang->fresh()->kondisi);
    }
}
