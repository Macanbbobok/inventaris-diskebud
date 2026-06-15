<?php

namespace App\Filament\Resources\MutasiBarangs\Pages;

use App\Filament\Resources\MutasiBarangs\MutasiBarangResource;
use App\Models\Barang;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class CreateMutasiBarang extends CreateRecord
{
    protected static string $resource = MutasiBarangResource::class;

    private int $createdRecordsCount = 1;

    private int $skippedRecordsCount = 0;

    protected function handleRecordCreation(array $data): Model
    {
        $barangIds = collect($data['barang_ids'] ?? [$data['barang_id'] ?? null])
            ->filter()
            ->unique()
            ->values();

        if ($barangIds->isEmpty()) {
            Notification::make()
                ->title('Mutasi tidak valid')
                ->body('Pilih minimal satu barang untuk dimutasi.')
                ->danger()
                ->send();

            $this->halt();
        }

        $records = collect();

        foreach ($barangIds as $barangId) {
            $barang = Barang::find($barangId);

            if (! $barang || ! $barang->ruangan_id || (int) $barang->ruangan_id === (int) $data['ruangan_tujuan_id']) {
                $this->skippedRecordsCount++;

                continue;
            }

            $payload = Arr::except($data, ['barang_ids']);
            $payload['barang_id'] = $barang->id;
            $payload['ruangan_asal_id'] = $barang->ruangan_id;
            $payload['kondisi_sebelum'] = $barang->kondisi;
            $payload['dilakukan_oleh'] = Auth::id();

            $record = static::getModel()::create($payload);

            $barang->update([
                'ruangan_id' => $data['ruangan_tujuan_id'],
                'kondisi' => $data['kondisi_sesudah'],
            ]);

            $records->push($record);
        }

        if ($records->isEmpty()) {
            Notification::make()
                ->title('Mutasi tidak valid')
                ->body('Semua barang yang dipilih sudah berada di ruangan tujuan atau belum memiliki ruangan asal.')
                ->danger()
                ->send();

            $this->halt();
        }

        $this->createdRecordsCount = $records->count();

        return $records->first();
    }

    protected function getCreatedNotification(): ?Notification
    {
        $title = $this->createdRecordsCount > 1
            ? "{$this->createdRecordsCount} mutasi barang berhasil"
            : 'Mutasi barang berhasil';

        $body = $this->createdRecordsCount > 1
            ? "{$this->createdRecordsCount} barang sudah dipindahkan ke ruangan tujuan."
            : $this->record->barang?->kode_nama . ' sudah dipindahkan ke ruangan tujuan.';

        if ($this->skippedRecordsCount > 0) {
            $body .= " {$this->skippedRecordsCount} barang dilewati karena sudah di ruangan tujuan atau belum memiliki ruangan asal.";
        }

        return Notification::make()
            ->title($title)
            ->body($body)
            ->success();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl();
    }
}
