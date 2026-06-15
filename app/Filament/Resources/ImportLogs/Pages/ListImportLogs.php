<?php

namespace App\Filament\Resources\ImportLogs\Pages;

use App\Filament\Resources\ImportLogs\ImportLogResource;
use App\Filament\Resources\ImportLogs\Widgets\ImportLogStatsOverview;
use Filament\Resources\Pages\ListRecords;

class ListImportLogs extends ListRecords
{
    protected static string $resource = ImportLogResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            ImportLogStatsOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
