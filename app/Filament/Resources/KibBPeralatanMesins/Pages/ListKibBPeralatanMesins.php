<?php

namespace App\Filament\Resources\KibBPeralatanMesins\Pages;

use App\Exports\BarangKibBTemplateExport;
use App\Filament\Resources\KibBPeralatanMesins\KibBPeralatanMesinResource;
use App\Filament\Resources\KibBPeralatanMesins\Widgets\KibBStatsOverview;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ListKibBPeralatanMesins extends ListRecords
{
    protected static string $resource = KibBPeralatanMesinResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            KibBStatsOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_template_barang_kib_b')
                ->label('Template Barang + KIB B')
                ->icon('heroicon-o-document-arrow-down')
                ->color('info')
                ->visible(fn () => in_array(Auth::user()?->role, ['admin', 'staff'], true))
                ->action(function () {
                    return Excel::download(new BarangKibBTemplateExport, 'template-barang-kib-b.xlsx');
                }),

            CreateAction::make()
                ->visible(fn () => in_array(Auth::user()?->role, ['admin', 'staff'], true)),
        ];
    }
}
