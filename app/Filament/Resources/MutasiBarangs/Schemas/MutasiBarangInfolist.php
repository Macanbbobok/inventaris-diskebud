<?php

namespace App\Filament\Resources\MutasiBarangs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class MutasiBarangInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ringkasan Mutasi')
                    ->icon('heroicon-o-arrows-right-left')
                    ->description('Riwayat perpindahan lokasi dan perubahan kondisi barang.')
                    ->schema([
                        TextEntry::make('barang.nama_barang')
                            ->label('Nama Barang')
                            ->size(TextSize::Large)
                            ->weight(FontWeight::Bold)
                            ->placeholder('-')
                            ->columnSpan([
                                'default' => 1,
                                'md' => 2,
                            ]),

                        TextEntry::make('barang.kode_barang')
                            ->label('Kode Barang')
                            ->badge()
                            ->copyable()
                            ->placeholder('-'),

                        TextEntry::make('tanggal_mutasi')
                            ->label('Tanggal Mutasi')
                            ->date('d M Y')
                            ->placeholder('-'),

                        TextEntry::make('petugas.name')
                            ->label('Dilakukan Oleh')
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
                        Section::make('Perpindahan Ruangan')
                            ->icon('heroicon-o-building-office-2')
                            ->description('Ruangan asal dan tujuan mutasi barang.')
                            ->schema([
                                TextEntry::make('ruanganAsal.nama_ruangan')
                                    ->label('Ruangan Asal')
                                    ->badge()
                                    ->color('gray')
                                    ->placeholder('-'),

                                TextEntry::make('ruanganTujuan.nama_ruangan')
                                    ->label('Ruangan Tujuan')
                                    ->badge()
                                    ->color('success')
                                    ->placeholder('-'),

                                TextEntry::make('ruanganAsal.kode_ruangan')
                                    ->label('Kode Ruangan Asal')
                                    ->placeholder('-'),

                                TextEntry::make('ruanganTujuan.kode_ruangan')
                                    ->label('Kode Ruangan Tujuan')
                                    ->placeholder('-'),
                            ])
                            ->columns([
                                'default' => 1,
                                'md' => 2,
                            ]),

                        Section::make('Kondisi Barang')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->description('Kondisi barang sebelum dan sesudah mutasi.')
                            ->schema([
                                TextEntry::make('kondisi_sebelum')
                                    ->label('Kondisi Sebelum')
                                    ->badge()
                                    ->formatStateUsing(fn (?string $state): string => self::formatCondition($state))
                                    ->color(fn (?string $state): string => self::conditionColor($state)),

                                TextEntry::make('kondisi_sesudah')
                                    ->label('Kondisi Sesudah')
                                    ->badge()
                                    ->formatStateUsing(fn (?string $state): string => self::formatCondition($state))
                                    ->color(fn (?string $state): string => self::conditionColor($state)),

                                TextEntry::make('alasan')
                                    ->label('Alasan Mutasi')
                                    ->placeholder('-')
                                    ->columnSpanFull(),
                            ])
                            ->columns([
                                'default' => 1,
                                'md' => 2,
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    private static function formatCondition(?string $condition): string
    {
        return match ($condition) {
            'baik' => 'Baik',
            'rusak_ringan' => 'Rusak Ringan',
            'rusak_berat' => 'Rusak Berat',
            default => $condition ?? '-',
        };
    }

    private static function conditionColor(?string $condition): string
    {
        return match ($condition) {
            'baik' => 'success',
            'rusak_ringan' => 'warning',
            'rusak_berat' => 'danger',
            default => 'gray',
        };
    }
}
