<?php

namespace App\Filament\Resources\MutasiBarangs\Pages;

use App\Filament\Resources\MutasiBarangs\MutasiBarangResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;

class ViewMutasiBarang extends ViewRecord
{
    protected static string $resource = MutasiBarangResource::class;

    protected Width | string | null $maxContentWidth = Width::Full;

    public function getTitle(): string
    {
        return $this->record->barang?->nama_barang ?? 'Mutasi Barang';
    }

    public function getSubheading(): ?string
    {
        return $this->record->tanggal_mutasi
            ? 'Tanggal mutasi: '.$this->record->tanggal_mutasi
            : null;
    }
}
