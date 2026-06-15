<?php

namespace App\Filament\Resources\MutasiBarangs\Pages;

use App\Filament\Resources\MutasiBarangs\MutasiBarangResource;
use Filament\Resources\Pages\EditRecord;

class EditMutasiBarang extends EditRecord
{
    protected static string $resource = MutasiBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
