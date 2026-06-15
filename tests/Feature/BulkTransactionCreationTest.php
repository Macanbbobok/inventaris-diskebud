<?php

namespace Tests\Feature;

use App\Filament\Resources\MutasiBarangs\Pages\CreateMutasiBarang;
use App\Filament\Resources\Pemeliharaans\Pages\CreatePemeliharaan;
use App\Models\Barang;
use App\Models\Bidang;
use App\Models\MutasiBarang;
use App\Models\Pemeliharaan;
use App\Models\Ruangan;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BulkTransactionCreationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_create_mutasi_barang_can_process_multiple_barang(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $this->actingAs($admin);

        $asal = Ruangan::create([
            'kode_ruangan' => 'R-001',
            'nama_ruangan' => 'Ruang Asal',
        ]);
        $tujuan = Ruangan::create([
            'kode_ruangan' => 'R-002',
            'nama_ruangan' => 'Ruang Tujuan',
        ]);
        $barangPertama = $this->createBarang('BRG-0001', $asal->id, 'rusak_ringan');
        $barangKedua = $this->createBarang('BRG-0002', $asal->id, 'baik');

        Livewire::test(CreateMutasiBarang::class)
            ->fillForm([
                'barang_ids' => [$barangPertama->id, $barangKedua->id],
                'ruangan_tujuan_id' => $tujuan->id,
                'tanggal_mutasi' => '2026-05-20',
                'alasan' => 'Penataan ulang ruangan.',
                'kondisi_sesudah' => 'baik',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertSame(2, MutasiBarang::count());
        $this->assertSame($tujuan->id, $barangPertama->fresh()->ruangan_id);
        $this->assertSame($tujuan->id, $barangKedua->fresh()->ruangan_id);
        $this->assertSame('baik', $barangPertama->fresh()->kondisi);
        $this->assertSame('baik', $barangKedua->fresh()->kondisi);
    }

    public function test_create_mutasi_barang_shows_selected_barang_origin_rooms(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $this->actingAs($admin);

        $asal = Ruangan::create([
            'kode_ruangan' => 'R-001',
            'nama_ruangan' => 'Ruang Asal',
        ]);
        $barang = $this->createBarang('BRG-0001', $asal->id, 'rusak_ringan');

        Livewire::test(CreateMutasiBarang::class)
            ->fillForm([
                'barang_ids' => [$barang->id],
            ])
            ->assertSee('Ruangan asal barang terpilih')
            ->assertSee('BRG-0001')
            ->assertSee('Ruang Asal')
            ->assertSee('Rusak Ringan');
    }

    public function test_create_pemeliharaan_can_process_multiple_barang(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $this->actingAs($admin);

        $ruangan = Ruangan::create([
            'kode_ruangan' => 'R-001',
            'nama_ruangan' => 'Ruang Sekretariat',
        ]);
        $barangPertama = $this->createBarang('BRG-0001', $ruangan->id, 'rusak_ringan');
        $barangKedua = $this->createBarang('BRG-0002', $ruangan->id, 'rusak_berat');

        Livewire::test(CreatePemeliharaan::class)
            ->fillForm([
                'barang_ids' => [$barangPertama->id, $barangKedua->id],
                'tanggal' => '2026-05-20',
                'jenis_pemeliharaan' => 'Perawatan berkala',
                'deskripsi' => 'Pembersihan dan pengecekan kondisi.',
                'biaya' => 50000,
                'kondisi_sesudah' => 'baik',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertSame(2, Pemeliharaan::count());
        $this->assertSame('baik', $barangPertama->fresh()->kondisi);
        $this->assertSame('baik', $barangKedua->fresh()->kondisi);
    }

    private function createBarang(string $kodeBarang, int $ruanganId, string $kondisi): Barang
    {
        $bidang = Bidang::firstOrCreate([
            'nama_bidang' => 'Sekretariat',
        ]);

        return Barang::withoutEvents(fn (): Barang => Barang::create([
            'kode_barang' => $kodeBarang,
            'nama_barang' => 'Barang ' . $kodeBarang,
            'bidang_id' => $bidang->id,
            'ruangan_id' => $ruanganId,
            'kondisi' => $kondisi,
            'status' => 'aktif',
            'harga_perolehan' => 1000000,
        ]));
    }
}
