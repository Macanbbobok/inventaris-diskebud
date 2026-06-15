<?php

namespace App\Filament\Resources\LaporanInventaris\Pages;

use App\Filament\Resources\LaporanInventaris\LaporanInventarisResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;

class ViewLaporanInventaris extends ViewRecord
{
    protected static string $resource = LaporanInventarisResource::class;

    protected Width | string | null $maxContentWidth = Width::Full;

    public function getTitle(): string
    {
        return 'Laporan Inventaris';
    }

    public function getSubheading(): ?string
    {
        return $this->record->tahun
            ? 'Periode tahun '.$this->record->tahun
            : null;
    }
}
