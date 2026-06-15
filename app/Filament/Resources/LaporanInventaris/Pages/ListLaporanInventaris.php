<?php

namespace App\Filament\Resources\LaporanInventaris\Pages;

use App\Filament\Resources\LaporanInventaris\LaporanInventarisResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListLaporanInventaris extends ListRecords
{
    protected static string $resource = LaporanInventarisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn () => in_array(Auth::user()?->role, ['admin', 'staff'], true)),
        ];
    }
}
