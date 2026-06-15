<?php

namespace App\Filament\Resources\Barangs\Pages;

use App\Exports\BarangExport;
use App\Exports\BarangKibBTemplateExport;
use App\Exports\BarangTemplateExport;
use App\Filament\Resources\Barangs\BarangResource;
use App\Filament\Resources\Barangs\Widgets\BarangStatsOverview;
use App\Imports\BarangImport;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ListBarangs extends ListRecords
{
    protected static string $resource = BarangResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            BarangStatsOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('download_template_barang')
                    ->label('Template Barang')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function () {
                        return Excel::download(new BarangTemplateExport, 'template-barang.xlsx');
                    }),

                Action::make('download_template_barang_kib_b')
                    ->label('Template Barang + KIB B')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function () {
                        return Excel::download(new BarangKibBTemplateExport, 'template-barang-kib-b.xlsx');
                    }),

                Action::make('import_excel')
                    ->label('Import Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        FileUpload::make('file')
                            ->required()
                            ->disk('public')
                            ->directory('imports')
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/vnd.ms-excel',
                            ]),
                    ])
                    ->action(function (array $data) {
                        $import = new BarangImport;
                        $pathFile = $data['file'];

                        Excel::import(
                            $import,
                            Storage::disk('public')->path($pathFile)
                        );

                        $import->simpanLog(
                            basename($pathFile),
                            $pathFile,
                        );

                        $notification = Notification::make()
                            ->title('Import Excel selesai')
                            ->body(
                                "Berhasil: {$import->berhasil}, duplikat: {$import->duplikat}, gagal: {$import->gagal}."
                            );

                        match (true) {
                            $import->gagal > 0 && $import->berhasil === 0 => $notification->danger(),
                            $import->gagal > 0 || $import->duplikat > 0 => $notification->warning(),
                            default => $notification->success(),
                        };

                        $notification->send();
                    }),

                Action::make('export_excel')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function () {
                        return Excel::download(new BarangExport, 'data-barang.xlsx');
                    }),
            ])
                ->label('Excel')
                ->icon('heroicon-o-table-cells')
                ->button()
                ->color('gray')
                ->visible(fn () => in_array(Auth::user()?->role, ['admin', 'staff'], true))
                ->dropdownWidth('16rem'),

            CreateAction::make()
                ->visible(fn () => in_array(Auth::user()?->role, ['admin', 'staff'], true)),
        ];
    }
}
