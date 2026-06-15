<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Bidang;
use App\Models\KibBPeralatanMesin;
use App\Models\Ruangan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KibBPeralatanMesinTest extends TestCase
{
    use RefreshDatabase;

    public function test_barang_is_marked_as_kib_b_after_kib_b_detail_is_created(): void
    {
        $barang = $this->createBarang();

        $this->assertNull($barang->jenis_kib);

        KibBPeralatanMesin::create([
            'barang_id' => $barang->id,
            'merk_type' => 'ASUS VivoBook',
        ]);

        $this->assertSame('B', $barang->fresh()->jenis_kib);
    }

    public function test_barang_is_unmarked_when_kib_b_detail_is_deleted(): void
    {
        $barang = $this->createBarang();

        $detail = KibBPeralatanMesin::create([
            'barang_id' => $barang->id,
            'merk_type' => 'ASUS VivoBook',
        ]);

        $detail->delete();

        $this->assertNull($barang->fresh()->jenis_kib);
    }

    private function createBarang(): Barang
    {
        $bidang = Bidang::create([
            'nama_bidang' => 'Sekretariat',
        ]);

        $ruangan = Ruangan::create([
            'kode_ruangan' => 'R-001',
            'nama_ruangan' => 'Ruang Sekretariat',
        ]);

        return Barang::withoutEvents(fn (): Barang => Barang::create([
            'kode_barang' => 'BRG-0001',
            'nama_barang' => 'Laptop Inventaris',
            'bidang_id' => $bidang->id,
            'ruangan_id' => $ruangan->id,
            'kondisi' => 'baik',
            'status' => 'aktif',
            'harga_perolehan' => 5000000,
        ]));
    }
}
