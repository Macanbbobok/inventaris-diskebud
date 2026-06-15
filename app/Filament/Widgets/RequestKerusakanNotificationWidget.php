<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\RequestKerusakans\RequestKerusakanResource;
use App\Services\RequestKerusakanNotificationService;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

class RequestKerusakanNotificationWidget extends TableWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = -10;

    public static function canView(): bool
    {
        return Auth::user()?->role === 'staff';
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Status Request Saya')
            ->query(fn () => $this->getNotificationQuery())
            ->columns([
                TextColumn::make('notifikasi')
                    ->label('Notifikasi')
                    ->state(fn (DatabaseNotification $record): string => $record->data['title'] ?? 'Notifikasi request')
                    ->description(fn (DatabaseNotification $record): ?string => $record->data['body'] ?? null)
                    ->wrap(),

                TextColumn::make('status_request')
                    ->label('Status')
                    ->state(fn (DatabaseNotification $record): ?string => match (data_get($record->data, 'viewData.status')) {
                        'diproses' => 'Diproses',
                        'selesai' => 'Selesai',
                        'ditolak' => 'Ditolak',
                        default => null,
                    })
                    ->badge()
                    ->color(fn (DatabaseNotification $record): string => $record->data['status'] ?? 'gray'),

                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->since()
                    ->sortable(),
            ])
            ->recordUrl(fn (DatabaseNotification $record): ?string => $this->getRequestUrl($record))
            ->emptyStateHeading('Belum ada update request')
            ->emptyStateDescription('Update dari admin untuk request yang kamu ajukan akan muncul di sini.')
            ->paginated(false)
            ->poll('10s');
    }

    private function getNotificationQuery()
    {
        $user = Auth::user();

        if (! $user) {
            return DatabaseNotification::query()->whereRaw('1 = 0');
        }

        return $user->unreadNotifications()
            ->where('data->format', 'filament')
            ->where('data->viewData->type', RequestKerusakanNotificationService::TYPE);
    }

    private function getRequestUrl(DatabaseNotification $notification): ?string
    {
        $requestKerusakanId = data_get($notification->data, 'viewData.request_kerusakan_id');

        if (! $requestKerusakanId) {
            return null;
        }

        return RequestKerusakanResource::getUrl('view', [
            'record' => $requestKerusakanId,
        ], false);
    }
}
