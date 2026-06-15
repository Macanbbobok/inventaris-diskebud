<?php

namespace App\Filament\Resources\ImportLogs\Pages;

use App\Filament\Resources\ImportLogs\ImportLogResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditImportLog extends EditRecord
{
    protected static string $resource = ImportLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(function (): bool {
                    $user = Auth::user();
                    $record = $this->getRecord();

                    return $user?->role === 'admin'
                        || (
                            $user?->role === 'staff'
                            && $record->diupload_oleh === $user->id
                        );
                }),
        ];
    }
}
