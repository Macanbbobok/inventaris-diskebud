<?php

use App\Services\LaporanInventarisPeriodikService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('inventaris:generate-periodic-reports {--date= : Tanggal acuan dalam format YYYY-MM-DD} {--force : Tetap buat laporan walaupun periode yang sama sudah ada}', function (LaporanInventarisPeriodikService $service, ?string $date = null, bool $force = false) {
    $result = $service->generateDueReports($date, $force);

    if (empty($result['due'])) {
        $this->info('Tidak ada laporan periodik yang jatuh tempo.');

        return 0;
    }

    foreach ($result['created'] as $item) {
        $this->info(
            'Dibuat: ' .
            $item['period']['label'] .
            ' -> ' .
            $item['record']->file_laporan
        );
    }

    foreach ($result['skipped'] as $item) {
        $this->line('Dilewati: ' . $item['period']['label'] . ' sudah tersedia.');
    }

    return 0;
})->purpose('Membuat laporan inventaris gabungan otomatis untuk periode yang sudah selesai.');

Schedule::command('inventaris:generate-periodic-reports')
    ->dailyAt('00:10');
