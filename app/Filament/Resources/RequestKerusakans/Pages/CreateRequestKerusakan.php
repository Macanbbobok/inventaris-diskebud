<?php

namespace App\Filament\Resources\RequestKerusakans\Pages;

use App\Filament\Resources\RequestKerusakans\RequestKerusakanResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateRequestKerusakan extends CreateRecord
{
    protected static string $resource = RequestKerusakanResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Request kerusakan diajukan')
            ->body($this->record->barang?->kode_nama . ' masuk daftar penanganan.')
            ->warning();
    }
}
