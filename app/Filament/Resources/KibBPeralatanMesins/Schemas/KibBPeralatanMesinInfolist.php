<?php

namespace App\Filament\Resources\KibBPeralatanMesins\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class KibBPeralatanMesinInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ringkasan KIB B')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->description('Data barang yang tercatat sebagai KIB B Peralatan dan Mesin.')
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

                        TextEntry::make('barang.status')
                            ->label('Status Barang')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => self::formatStatus($state))
                            ->color(fn (?string $state): string => self::statusColor($state)),

                        TextEntry::make('barang.kondisi')
                            ->label('Kondisi Barang')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => self::formatCondition($state))
                            ->color(fn (?string $state): string => self::conditionColor($state)),

                        TextEntry::make('barang.harga_perolehan')
                            ->label('Harga Perolehan')
                            ->formatStateUsing(fn ($state): string => self::formatRupiah($state))
                            ->weight(FontWeight::Bold),
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
                        Section::make('Identitas Teknis')
                            ->icon('heroicon-o-cpu-chip')
                            ->description('Spesifikasi teknis barang KIB B.')
                            ->schema([
                                TextEntry::make('merk_type')
                                    ->label('Merk / Type')
                                    ->weight(FontWeight::Bold)
                                    ->placeholder('-'),

                                TextEntry::make('ukuran')
                                    ->label('Ukuran')
                                    ->placeholder('-'),

                                TextEntry::make('bahan')
                                    ->label('Bahan')
                                    ->placeholder('-'),

                                TextEntry::make('no_seri')
                                    ->label('No Seri')
                                    ->copyable()
                                    ->placeholder('-'),

                                TextEntry::make('spesifikasi')
                                    ->label('Spesifikasi')
                                    ->placeholder('-')
                                    ->columnSpanFull(),
                            ])
                            ->columns([
                                'default' => 1,
                                'md' => 2,
                            ]),

                        Section::make('Lokasi & Penanggung Jawab')
                            ->icon('heroicon-o-map-pin')
                            ->description('Lokasi barang dan bidang penanggung jawab.')
                            ->schema([
                                TextEntry::make('barang.bidang.nama_bidang')
                                    ->label('Bidang')
                                    ->badge()
                                    ->color('info')
                                    ->placeholder('-'),

                                TextEntry::make('barang.ruangan.nama_ruangan')
                                    ->label('Ruangan')
                                    ->placeholder('-'),

                                TextEntry::make('barang.ruangan.kode_ruangan')
                                    ->label('Kode Ruangan')
                                    ->badge()
                                    ->placeholder('-'),

                                TextEntry::make('barang.tanggal_perolehan')
                                    ->label('Tanggal Perolehan')
                                    ->date('d M Y')
                                    ->placeholder('-'),
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

    private static function formatStatus(?string $status): string
    {
        return match ($status) {
            'aktif' => 'Aktif',
            'dipinjam' => 'Dipinjam',
            'dihapus' => 'Dihapus',
            default => $status ?? '-',
        };
    }

    private static function statusColor(?string $status): string
    {
        return match ($status) {
            'aktif' => 'success',
            'dipinjam' => 'warning',
            'dihapus' => 'danger',
            default => 'gray',
        };
    }

    private static function formatRupiah(mixed $state): string
    {
        if (blank($state)) {
            return '-';
        }

        return 'Rp '.number_format((float) $state, 0, ',', '.');
    }
}
