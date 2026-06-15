<?php

namespace App\Filament\Resources\MutasiBarangs\Pages;

use App\Filament\Resources\MutasiBarangs\MutasiBarangResource;
use App\Filament\Resources\MutasiBarangs\Widgets\MutasiBarangStatsOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListMutasiBarangs extends ListRecords
{
    protected static string $resource = MutasiBarangResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            MutasiBarangStatsOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn () => in_array(Auth::user()?->role, ['admin', 'staff'], true)),
        ];
    }
}
