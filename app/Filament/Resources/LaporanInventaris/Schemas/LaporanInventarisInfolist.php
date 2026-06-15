<?php

namespace App\Filament\Resources\LaporanInventaris\Schemas;

use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Illuminate\Support\Facades\Storage;

class LaporanInventarisInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ringkasan Laporan')
                    ->icon('heroicon-o-document-text')
                    ->description('Informasi jenis, periode, dan pembuat laporan inventaris.')
                    ->schema([
                        TextEntry::make('jenis_laporan')
                            ->label('Jenis Laporan')
                            ->size(TextSize::Large)
                            ->weight(FontWeight::Bold)
                            ->formatStateUsing(fn (?string $state): string => self::formatReportType($state)),

                        TextEntry::make('periode')
                            ->label('Periode')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => self::formatPeriod($state))
                            ->color(fn (?string $state): string => self::periodColor($state)),

                        TextEntry::make('bulan')
                            ->label('Bulan')
                            ->formatStateUsing(fn ($state): string => self::formatMonth($state))
                            ->placeholder('-'),

                        TextEntry::make('tahun')
                            ->label('Tahun')
                            ->badge()
                            ->color('gray')
                            ->placeholder('-'),

                        TextEntry::make('pembuat.name')
                            ->label('Dibuat Oleh')
                            ->placeholder('-'),

                        TextEntry::make('created_at')
                            ->label('Tanggal Dibuat')
                            ->dateTime('d M Y H:i')
                            ->placeholder('-'),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                        'xl' => 4,
                    ])
                    ->columnSpanFull(),

                Grid::make([
                    'default' => 1,
                    'xl' => 2,
                ])
                    ->schema([
                        Section::make('File Laporan')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->description('File hasil generate laporan yang dapat digunakan untuk arsip.')
                            ->headerActions([
                                Action::make('download_laporan')
                                    ->label('Download')
                                    ->icon('heroicon-o-arrow-down-tray')
                                    ->color('success')
                                    ->url(fn ($record): ?string => self::fileExists($record->file_laporan)
                                        ? asset('storage/' . $record->file_laporan)
                                        : null)
                                    ->openUrlInNewTab()
                                    ->visible(fn ($record): bool => self::fileExists($record->file_laporan)),
                            ])
                            ->schema([
                                TextEntry::make('file_laporan_status')
                                    ->label('Status File')
                                    ->state(fn ($record): string => self::fileExists($record->file_laporan)
                                        ? 'File laporan tersedia'
                                        : 'File laporan belum tersedia')
                                    ->badge()
                                    ->color(fn ($record): string => self::fileExists($record->file_laporan)
                                        ? 'success'
                                        : 'gray'),

                                TextEntry::make('file_laporan_path')
                                    ->label('Path File')
                                    ->state(fn ($record): ?string => $record->file_laporan)
                                    ->copyable()
                                    ->placeholder('-')
                                    ->visible(fn ($record): bool => filled($record->file_laporan)),
                            ]),

                        Section::make('Metadata')
                            ->icon('heroicon-o-information-circle')
                            ->description('Jejak perubahan data laporan.')
                            ->schema([
                                TextEntry::make('updated_at')
                                    ->label('Terakhir Diperbarui')
                                    ->dateTime('d M Y H:i')
                                    ->placeholder('-'),

                                TextEntry::make('id')
                                    ->label('ID Laporan')
                                    ->badge()
                                    ->color('gray'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    private static function formatReportType(?string $type): string
    {
        return match ($type) {
            'barang' => 'Laporan Barang',
            'kib_b' => 'Laporan KIB B Peralatan Mesin',
            'mutasi' => 'Laporan Mutasi Barang',
            'pemeliharaan' => 'Laporan Pemeliharaan',
            'gabungan' => 'Laporan Gabungan',
            default => $type ?? '-',
        };
    }

    private static function formatPeriod(?string $period): string
    {
        return match ($period) {
            'bulanan' => 'Bulanan',
            'triwulanan' => 'Triwulanan',
            'semesteran' => 'Semesteran',
            'tahunan' => 'Tahunan',
            default => $period ?? '-',
        };
    }

    private static function periodColor(?string $period): string
    {
        return match ($period) {
            'bulanan' => 'info',
            'triwulanan' => 'warning',
            'semesteran' => 'primary',
            'tahunan' => 'success',
            default => 'gray',
        };
    }

    private static function formatMonth(mixed $month): string
    {
        if (blank($month)) {
            return '-';
        }

        return match ((int) $month) {
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
            default => (string) $month,
        };
    }

    private static function fileExists(?string $path): bool
    {
        return filled($path) && Storage::disk('public')->exists($path);
    }
}
