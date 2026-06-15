<?php

namespace App\Filament\Resources\Pemeliharaans\Pages;

use App\Filament\Resources\Pemeliharaans\PemeliharaanResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;

class ViewPemeliharaan extends ViewRecord
{
    protected static string $resource = PemeliharaanResource::class;

    protected Width | string | null $maxContentWidth = Width::Full;

    public function getTitle(): string
    {
        return $this->record->barang?->nama_barang ?? 'Pemeliharaan';
    }

    public function getSubheading(): ?string
    {
        return $this->record->jenis_pemeliharaan
            ? 'Jenis: '.$this->record->jenis_pemeliharaan
            : null;
    }
}
