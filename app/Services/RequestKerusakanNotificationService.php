<?php

namespace App\Services;

use App\Filament\Resources\RequestKerusakans\RequestKerusakanResource;
use App\Models\RequestKerusakan;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\DatabaseNotification;

class RequestKerusakanNotificationService
{
    public const TYPE = self::TYPE_STATUS;

    public const TYPE_CREATED = 'request_kerusakan_baru';

    public const TYPE_STATUS = 'request_kerusakan_status';

    private const NOTIFIABLE_STATUSES = [
        'diproses',
        'selesai',
        'ditolak',
    ];

    private const FINAL_STATUSES = [
        'selesai',
        'ditolak',
    ];

    public function sendCreatedNotification(RequestKerusakan $requestKerusakan): void
    {
        $requestKerusakan->loadMissing(['barang', 'pelapor']);

        $admins = User::query()
            ->where('role', 'admin')
            ->get();

        if ($admins->isEmpty()) {
            return;
        }

        $notification = FilamentNotification::make()
            ->title('Request kerusakan baru')
            ->body($this->getCreatedBody($requestKerusakan))
            ->warning()
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->link()
                    ->markAsRead()
                    ->url($this->getRequestUrl($requestKerusakan)),
            ])
            ->viewData([
                'type' => self::TYPE_CREATED,
                'request_kerusakan_id' => $requestKerusakan->id,
                'status' => $requestKerusakan->status,
            ]);

        $admins->each(function (User $admin) use ($notification): mixed {
            $admin->notifyNow($notification->toDatabase());

            return null;
        });
    }

    public function sendStatusNotification(RequestKerusakan $requestKerusakan): void
    {
        if (! in_array($requestKerusakan->status, self::NOTIFIABLE_STATUSES, true)) {
            return;
        }

        $requestKerusakan->loadMissing(['barang', 'pelapor']);

        $recipient = $requestKerusakan->pelapor;

        if (! $recipient) {
            return;
        }

        $notification = FilamentNotification::make()
            ->title($this->getTitle($requestKerusakan->status))
            ->body($this->getBody($requestKerusakan))
            ->status($this->getNotificationStatus($requestKerusakan->status))
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->link()
                    ->url($this->getRequestUrl($requestKerusakan)),
            ])
            ->viewData([
                'type' => self::TYPE_STATUS,
                'request_kerusakan_id' => $requestKerusakan->id,
                'status' => $requestKerusakan->status,
            ]);

        $recipient->notifyNow($notification->toDatabase());
    }

    public function markCreatedNotificationsAsRead(RequestKerusakan $requestKerusakan, User $user): int
    {
        if ($user->role !== 'admin') {
            return 0;
        }

        return $this->markUserNotificationsAsRead($requestKerusakan, $user, self::TYPE_CREATED);
    }

    public function markCreatedNotificationsAsReadForAdmins(RequestKerusakan $requestKerusakan): int
    {
        $notifications = DatabaseNotification::query()
            ->whereNull('read_at')
            ->where('data->viewData->type', self::TYPE_CREATED)
            ->get()
            ->filter(fn (DatabaseNotification $notification): bool => (int) data_get($notification->data, 'viewData.request_kerusakan_id') === $requestKerusakan->id);

        $notifications->each->markAsRead();

        return $notifications->count();
    }

    public function markFinalStatusNotificationsAsRead(RequestKerusakan $requestKerusakan, User $user): int
    {
        if (! $this->isFinalStatus($requestKerusakan->status)) {
            return 0;
        }

        if ($requestKerusakan->dilaporkan_oleh !== $user->id) {
            return 0;
        }

        return $this->markUserNotificationsAsRead($requestKerusakan, $user, self::TYPE_STATUS);
    }

    public function isFinalStatus(?string $status): bool
    {
        return in_array($status, self::FINAL_STATUSES, true);
    }

    private function markUserNotificationsAsRead(RequestKerusakan $requestKerusakan, User $user, string $type): int
    {
        $notifications = $user->unreadNotifications()
            ->where('data->viewData->type', $type)
            ->get()
            ->filter(fn ($notification): bool => (int) data_get($notification->data, 'viewData.request_kerusakan_id') === $requestKerusakan->id);

        $notifications->each->markAsRead();

        return $notifications->count();
    }

    private function getTitle(string $status): string
    {
        return match ($status) {
            'diproses' => 'Request kerusakan diproses',
            'selesai' => 'Request kerusakan selesai',
            'ditolak' => 'Request kerusakan ditolak',
            default => 'Update request kerusakan',
        };
    }

    private function getCreatedBody(RequestKerusakan $requestKerusakan): string
    {
        $barang = $requestKerusakan->barang?->kode_nama ?? 'Barang inventaris';
        $pelapor = $requestKerusakan->pelapor?->name ?? 'Staff';

        return "{$pelapor} mengajukan request kerusakan untuk {$barang}.";
    }

    private function getBody(RequestKerusakan $requestKerusakan): string
    {
        $barang = $requestKerusakan->barang?->kode_nama ?? 'Barang inventaris';

        return match ($requestKerusakan->status) {
            'diproses' => "{$barang} sedang diproses oleh petugas.",
            'selesai' => "{$barang} sudah selesai ditangani.",
            'ditolak' => "{$barang} tidak dilanjutkan ke pemeliharaan.",
            default => "{$barang} mendapatkan pembaruan status.",
        };
    }

    private function getNotificationStatus(string $status): string
    {
        return match ($status) {
            'diproses' => 'info',
            'selesai' => 'success',
            'ditolak' => 'danger',
            default => 'gray',
        };
    }

    private function getRequestUrl(RequestKerusakan $requestKerusakan): string
    {
        return RequestKerusakanResource::getUrl('view', [
            'record' => $requestKerusakan,
        ], false);
    }
}
