<?php

namespace App\Filament\Resources\Pemeliharaans\Pages;

use App\Filament\Resources\Pemeliharaans\PemeliharaanResource;
use App\Filament\Resources\Pemeliharaans\Widgets\PemeliharaanStatsOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListPemeliharaans extends ListRecords
{
    protected static string $resource = PemeliharaanResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            PemeliharaanStatsOverview::class,
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
