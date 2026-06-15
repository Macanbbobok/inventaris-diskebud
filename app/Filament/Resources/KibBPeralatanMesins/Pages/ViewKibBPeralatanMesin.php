<?php

namespace App\Filament\Resources\KibBPeralatanMesins\Pages;

use App\Filament\Resources\KibBPeralatanMesins\KibBPeralatanMesinResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;

class ViewKibBPeralatanMesin extends ViewRecord
{
    protected static string $resource = KibBPeralatanMesinResource::class;

    protected Width | string | null $maxContentWidth = Width::Full;

    public function getTitle(): string
    {
        return $this->record->barang?->nama_barang ?? 'Detail KIB B';
    }

    public function getSubheading(): ?string
    {
        return $this->record->barang?->kode_barang
            ? 'Kode barang: '.$this->record->barang->kode_barang
            : null;
    }
}
