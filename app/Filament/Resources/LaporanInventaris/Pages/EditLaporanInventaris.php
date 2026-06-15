<?php

namespace App\Filament\Resources\LaporanInventaris\Pages;

use App\Filament\Resources\LaporanInventaris\LaporanInventarisResource;
use Filament\Resources\Pages\EditRecord;


class EditLaporanInventaris extends EditRecord
{
    protected static string $resource = LaporanInventarisResource::class;

    protected function getHeaderActions(): array
    {
        return [
           //
        ];
    }
}
