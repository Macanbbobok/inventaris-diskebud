<?php

namespace App\Filament\Resources\RequestKerusakans\Pages;

use App\Filament\Resources\RequestKerusakans\RequestKerusakanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListRequestKerusakans extends ListRecords
{
    protected static string $resource = RequestKerusakanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn () => in_array(Auth::user()?->role, ['admin', 'staff'], true)),
        ];
    }
}
