<?php

namespace App\Filament\Resources\RequestKerusakans\Pages;

use App\Filament\Resources\RequestKerusakans\RequestKerusakanResource;
use App\Services\RequestKerusakanNotificationService;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Auth;

class ViewRequestKerusakan extends ViewRecord
{
    protected static string $resource = RequestKerusakanResource::class;

    protected Width | string | null $maxContentWidth = Width::Full;

    public function getTitle(): string
    {
        return $this->record->barang?->nama_barang ?? 'Request Kerusakan';
    }

    public function getSubheading(): ?string
    {
        return $this->record->tanggal_laporan
            ? 'Tanggal laporan: '.$this->record->tanggal_laporan
            : null;
    }

    public function mount(int | string $record): void
    {
        parent::mount($record);

        $user = Auth::user();

        if (! $user) {
            return;
        }

        $notificationService = app(RequestKerusakanNotificationService::class);

        $notificationService->markCreatedNotificationsAsRead($this->record, $user);
        $notificationService->markFinalStatusNotificationsAsRead($this->record, $user);
    }
}
