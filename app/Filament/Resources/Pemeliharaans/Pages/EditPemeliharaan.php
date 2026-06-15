<?php

namespace App\Filament\Resources\Pemeliharaans\Pages;

use App\Filament\Resources\Pemeliharaans\PemeliharaanResource;
use Filament\Resources\Pages\EditRecord;

class EditPemeliharaan extends EditRecord
{
    protected static string $resource = PemeliharaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
        //
        ];
    }
}
