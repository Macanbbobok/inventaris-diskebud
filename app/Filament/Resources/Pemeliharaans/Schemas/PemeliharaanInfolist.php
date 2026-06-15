<?php

namespace App\Filament\Resources\Pemeliharaans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class PemeliharaanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ringkasan Pemeliharaan')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->description('Aktivitas pemeliharaan dan kondisi akhir barang.')
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

                        TextEntry::make('tanggal')
                            ->label('Tanggal')
                            ->date('d M Y')
                            ->placeholder('-'),

                        TextEntry::make('jenis_pemeliharaan')
                            ->label('Jenis Pemeliharaan')
                            ->badge()
                            ->color('info')
                            ->placeholder('-'),

                        TextEntry::make('kondisi_sesudah')
                            ->label('Kondisi Sesudah')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => self::formatCondition($state))
                            ->color(fn (?string $state): string => self::conditionColor($state)),
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
                        Section::make('Biaya & Catatan')
                            ->icon('heroicon-o-banknotes')
                            ->description('Biaya pemeliharaan dan catatan pekerjaan.')
                            ->schema([
                                TextEntry::make('biaya')
                                    ->label('Biaya')
                                    ->formatStateUsing(fn ($state): string => self::formatRupiah($state))
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('deskripsi')
                                    ->label('Deskripsi')
                                    ->placeholder('-')
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Request & Petugas')
                            ->icon('heroicon-o-user-circle')
                            ->description('Sumber request kerusakan dan petugas pelaksana.')
                            ->schema([
                                TextEntry::make('requestKerusakan.status')
                                    ->label('Status Request')
                                    ->badge()
                                    ->formatStateUsing(fn (?string $state): string => self::formatRequestStatus($state))
                                    ->color(fn (?string $state): string => self::requestStatusColor($state)),

                                TextEntry::make('requestKerusakan.tingkat_kerusakan')
                                    ->label('Tingkat Kerusakan')
                                    ->badge()
                                    ->formatStateUsing(fn (?string $state): string => self::formatDamageLevel($state))
                                    ->color(fn (?string $state): string => self::damageColor($state)),

                                TextEntry::make('petugas.name')
                                    ->label('Dilakukan Oleh')
                                    ->placeholder('-'),

                                TextEntry::make('created_at')
                                    ->label('Waktu Input')
                                    ->dateTime('d M Y H:i')
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

    private static function formatRequestStatus(?string $status): string
    {
        return match ($status) {
            'diajukan' => 'Diajukan',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            default => $status ?? '-',
        };
    }

    private static function requestStatusColor(?string $status): string
    {
        return match ($status) {
            'diajukan' => 'warning',
            'diproses' => 'info',
            'selesai' => 'success',
            'ditolak' => 'danger',
            default => 'gray',
        };
    }

    private static function formatDamageLevel(?string $level): string
    {
        return match ($level) {
            'ringan' => 'Ringan',
            'sedang' => 'Sedang',
            'berat' => 'Berat',
            default => $level ?? '-',
        };
    }

    private static function damageColor(?string $level): string
    {
        return match ($level) {
            'ringan' => 'warning',
            'sedang' => 'info',
            'berat' => 'danger',
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
