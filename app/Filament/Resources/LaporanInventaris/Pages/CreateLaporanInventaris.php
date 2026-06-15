<?php

namespace App\Filament\Resources\LaporanInventaris\Pages;

use App\Filament\Resources\LaporanInventaris\LaporanInventarisResource;
use App\Services\LaporanInventarisGenerator;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateLaporanInventaris extends CreateRecord
{
    protected static string $resource = LaporanInventarisResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(LaporanInventarisGenerator::class)->generate($data);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Laporan berhasil dibuat')
            ->body('File PDF sudah tersimpan dan siap diunduh.')
            ->success();
    }
}
