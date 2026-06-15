<?php

namespace Tests\Feature;

use App\Models\LaporanInventaris;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GeneratePeriodicReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_due_reports_after_periods_end(): void
    {
        Storage::fake('public');

        $this->artisan('inventaris:generate-periodic-reports', [
            '--date' => '2026-07-01',
        ])->assertExitCode(0);

        $this->assertDatabaseHas('laporan_inventaris', [
            'jenis_laporan' => 'gabungan',
            'periode' => 'bulanan',
            'bulan' => 6,
            'tahun' => 2026,
        ]);

        $this->assertDatabaseHas('laporan_inventaris', [
            'jenis_laporan' => 'gabungan',
            'periode' => 'triwulanan',
            'bulan' => 4,
            'tahun' => 2026,
        ]);

        $this->assertDatabaseHas('laporan_inventaris', [
            'jenis_laporan' => 'gabungan',
            'periode' => 'semesteran',
            'bulan' => 1,
            'tahun' => 2026,
        ]);

        $this->assertSame(3, LaporanInventaris::count());

        LaporanInventaris::query()->each(function (LaporanInventaris $laporan): void {
            Storage::disk('public')->assertExists($laporan->file_laporan);
        });
    }

    public function test_it_skips_existing_periodic_reports(): void
    {
        Storage::fake('public');

        $this->artisan('inventaris:generate-periodic-reports', [
            '--date' => '2026-07-01',
        ])->assertExitCode(0);

        $this->artisan('inventaris:generate-periodic-reports', [
            '--date' => '2026-07-01',
        ])->assertExitCode(0);

        $this->assertSame(3, LaporanInventaris::count());
    }

    public function test_it_does_not_generate_reports_when_no_period_is_due(): void
    {
        Storage::fake('public');

        $this->artisan('inventaris:generate-periodic-reports', [
            '--date' => '2026-07-02',
        ])->assertExitCode(0);

        $this->assertSame(0, LaporanInventaris::count());
    }
}
