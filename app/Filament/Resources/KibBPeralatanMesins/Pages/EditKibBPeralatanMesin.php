<?php

namespace App\Filament\Resources\KibBPeralatanMesins\Pages;

use App\Filament\Resources\KibBPeralatanMesins\KibBPeralatanMesinResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditKibBPeralatanMesin extends EditRecord
{
    protected static string $resource = KibBPeralatanMesinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => in_array(Auth::user()?->role, ['admin', 'staff'], true)),
        ];
    }
}
