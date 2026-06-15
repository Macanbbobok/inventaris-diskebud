<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Bidang;
use App\Models\Pemeliharaan;
use App\Models\RequestKerusakan;
use App\Models\Ruangan;
use App\Models\User;
use App\Services\RequestKerusakanNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RequestKerusakanTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_request_kerusakan_marks_barang_as_damaged(): void
    {
        $barang = $this->createBarang();

        RequestKerusakan::create([
            'barang_id' => $barang->id,
            'tanggal_laporan' => '2026-05-20',
            'tingkat_kerusakan' => 'berat',
            'deskripsi_kerusakan' => 'Tidak dapat dinyalakan.',
        ]);

        $this->assertSame('rusak_berat', $barang->fresh()->kondisi);
    }

    public function test_creating_pemeliharaan_from_request_marks_request_as_done(): void
    {
        $barang = $this->createBarang();

        $request = RequestKerusakan::create([
            'barang_id' => $barang->id,
            'tanggal_laporan' => '2026-05-20',
            'tingkat_kerusakan' => 'ringan',
            'deskripsi_kerusakan' => 'Hasil cetak kurang jelas.',
        ]);

        Pemeliharaan::create([
            'barang_id' => $barang->id,
            'request_kerusakan_id' => $request->id,
            'tanggal' => '2026-05-21',
            'jenis_pemeliharaan' => 'Perbaikan',
            'biaya' => 150000,
            'kondisi_sesudah' => 'baik',
        ]);

        $this->assertSame('selesai', $request->fresh()->status);
        $this->assertSame('baik', $barang->fresh()->kondisi);
    }

    public function test_status_change_sends_database_notification_to_reporter(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);
        $barang = $this->createBarang();

        $request = RequestKerusakan::create([
            'barang_id' => $barang->id,
            'tanggal_laporan' => '2026-05-20',
            'tingkat_kerusakan' => 'ringan',
            'deskripsi_kerusakan' => 'Mouse tidak responsif.',
            'dilaporkan_oleh' => $staff->id,
        ]);

        $request->update([
            'status' => 'diproses',
        ]);

        $notification = $staff->unreadNotifications()->first();

        $this->assertNotNull($notification);
        $this->assertSame('Request kerusakan diproses', $notification->data['title']);
        $this->assertSame(RequestKerusakanNotificationService::TYPE, $notification->data['viewData']['type']);
        $this->assertSame($request->id, $notification->data['viewData']['request_kerusakan_id']);
    }

    public function test_creating_request_kerusakan_sends_database_notification_to_admin(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);
        $barang = $this->createBarang();

        $request = RequestKerusakan::create([
            'barang_id' => $barang->id,
            'tanggal_laporan' => '2026-05-20',
            'tingkat_kerusakan' => 'berat',
            'deskripsi_kerusakan' => 'Monitor tidak menyala.',
            'dilaporkan_oleh' => $staff->id,
        ]);

        $notification = $admin->unreadNotifications()->first();

        $this->assertNotNull($notification);
        $this->assertSame('Request kerusakan baru', $notification->data['title']);
        $this->assertSame(RequestKerusakanNotificationService::TYPE_CREATED, $notification->data['viewData']['type']);
        $this->assertSame($request->id, $notification->data['viewData']['request_kerusakan_id']);
        $this->assertStringStartsWith('/dashboard/request-kerusakans/', $notification->data['actions'][0]['url']);
    }

    public function test_admin_request_notification_is_marked_read_when_request_is_handled(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);
        $barang = $this->createBarang();

        $request = RequestKerusakan::create([
            'barang_id' => $barang->id,
            'tanggal_laporan' => '2026-05-20',
            'tingkat_kerusakan' => 'ringan',
            'deskripsi_kerusakan' => 'Speaker tidak berbunyi.',
            'dilaporkan_oleh' => $staff->id,
        ]);

        $this->assertSame(1, $admin->unreadNotifications()->count());

        $request->update([
            'status' => 'diproses',
        ]);

        $this->assertSame(0, $admin->unreadNotifications()->count());
        $this->assertSame(1, $staff->unreadNotifications()->count());
    }

    public function test_final_status_notifications_are_marked_read_after_reporter_views_request(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);
        $barang = $this->createBarang();

        $request = RequestKerusakan::create([
            'barang_id' => $barang->id,
            'tanggal_laporan' => '2026-05-20',
            'tingkat_kerusakan' => 'sedang',
            'deskripsi_kerusakan' => 'Keyboard sering tidak terbaca.',
            'dilaporkan_oleh' => $staff->id,
        ]);

        $request->update([
            'status' => 'diproses',
        ]);
        $request->update([
            'status' => 'selesai',
        ]);

        $this->assertSame(2, $staff->unreadNotifications()->count());

        $markedAsRead = app(RequestKerusakanNotificationService::class)
            ->markFinalStatusNotificationsAsRead($request->fresh(), $staff);

        $this->assertSame(2, $markedAsRead);
        $this->assertSame(0, $staff->unreadNotifications()->count());
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
            'nama_barang' => 'Printer Administrasi',
            'bidang_id' => $bidang->id,
            'ruangan_id' => $ruangan->id,
            'kondisi' => 'baik',
            'status' => 'aktif',
            'harga_perolehan' => 2500000,
        ]));
    }
}
