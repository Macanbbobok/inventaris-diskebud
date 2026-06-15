<?php

namespace App\Filament\Resources\Pemeliharaans\Pages;

use App\Filament\Resources\Pemeliharaans\PemeliharaanResource;
use App\Models\RequestKerusakan;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class CreatePemeliharaan extends CreateRecord
{
    protected static string $resource = PemeliharaanResource::class;

    private int $createdRecordsCount = 1;

    protected function handleRecordCreation(array $data): Model
    {
        $requestKerusakan = filled($data['request_kerusakan_id'] ?? null)
            ? RequestKerusakan::find($data['request_kerusakan_id'])
            : null;

        $barangIds = collect($data['barang_ids'] ?? [$data['barang_id'] ?? null])
            ->when(
                $requestKerusakan,
                fn ($barangIds) => $barangIds->push($requestKerusakan->barang_id),
            )
            ->filter()
            ->unique()
            ->values();

        if ($barangIds->isEmpty()) {
            Notification::make()
                ->title('Pemeliharaan tidak valid')
                ->body('Pilih minimal satu barang untuk pemeliharaan.')
                ->danger()
                ->send();

            $this->halt();
        }

        $records = collect();

        foreach ($barangIds as $barangId) {
            $payload = Arr::except($data, ['barang_ids']);
            $payload['barang_id'] = $barangId;
            $payload['request_kerusakan_id'] = $requestKerusakan && (int) $requestKerusakan->barang_id === (int) $barangId
                ? $requestKerusakan->id
                : null;
            $payload['dilakukan_oleh'] = Auth::id();

            $records->push(static::getModel()::create($payload));
        }

        $this->createdRecordsCount = $records->count();

        return $records->first();
    }

    protected function getCreatedNotification(): ?Notification
    {
        $title = $this->createdRecordsCount > 1
            ? "{$this->createdRecordsCount} pemeliharaan dicatat"
            : 'Pemeliharaan dicatat';

        $body = $this->createdRecordsCount > 1
            ? "{$this->createdRecordsCount} barang diperbarui menjadi " . $this->formatCondition($this->record->kondisi_sesudah) . '.'
            : $this->record->barang?->kode_nama . ' diperbarui menjadi ' . $this->formatCondition($this->record->kondisi_sesudah) . '.';

        return Notification::make()
            ->title($title)
            ->body($body)
            ->success();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl();
    }

    private function formatCondition(?string $condition): string
    {
        return match ($condition) {
            'baik' => 'baik',
            'rusak_ringan' => 'rusak ringan',
            'rusak_berat' => 'rusak berat',
            default => $condition ?? '-',
        };
    }
}
