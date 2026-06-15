<?php

namespace App\Filament\Resources\Barangs\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Illuminate\Support\Facades\Storage;

class BarangInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ringkasan Barang')
                    ->icon('heroicon-o-cube')
                    ->description('Identitas, status, dan nilai aset.')
                    ->schema([
                        TextEntry::make('nama_barang')
                            ->label('Nama Barang')
                            ->size(TextSize::Large)
                            ->weight(FontWeight::Bold)
                            ->columnSpan([
                                'default' => 1,
                                'md' => 2,
                            ]),

                        TextEntry::make('kode_barang')
                            ->label('Kode Barang')
                            ->badge()
                            ->copyable(),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'aktif' => 'Aktif',
                                'dipinjam' => 'Dipinjam',
                                'dihapus' => 'Dihapus',
                                default => $state,
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'aktif' => 'success',
                                'dipinjam' => 'warning',
                                'dihapus' => 'danger',
                                default => 'gray',
                            }),

                        TextEntry::make('kondisi')
                            ->label('Kondisi')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'baik' => 'Baik',
                                'rusak_ringan' => 'Rusak Ringan',
                                'rusak_berat' => 'Rusak Berat',
                                default => $state,
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'baik' => 'success',
                                'rusak_ringan' => 'warning',
                                'rusak_berat' => 'danger',
                                default => 'gray',
                            }),

                        TextEntry::make('tahun_perolehan')
                            ->label('Tahun Perolehan')
                            ->placeholder('-'),

                        TextEntry::make('tanggal_perolehan')
                            ->label('Tanggal Perolehan')
                            ->date('d M Y')
                            ->placeholder('-'),

                        TextEntry::make('harga_perolehan')
                            ->label('Harga Perolehan')
                            ->formatStateUsing(fn ($state): string => static::formatRupiah($state))
                            ->weight(FontWeight::Bold),

                        TextEntry::make('creator.name')
                            ->label('Dibuat Oleh')
                            ->placeholder('-'),

                        TextEntry::make('created_at')
                            ->label('Tanggal Input')
                            ->dateTime('d M Y H:i'),
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
                        Section::make('Lokasi & Penanggung Jawab')
                            ->icon('heroicon-o-map-pin')
                            ->description('Lokasi fisik barang dan bidang yang bertanggung jawab.')
                            ->schema([
                                TextEntry::make('bidang.nama_bidang')
                                    ->label('Bidang Penanggung Jawab')
                                    ->badge()
                                    ->color('info')
                                    ->placeholder('-'),

                                TextEntry::make('ruangan.nama_ruangan')
                                    ->label('Ruangan')
                                    ->placeholder('-'),

                                TextEntry::make('ruangan.kode_ruangan')
                                    ->label('Kode Ruangan')
                                    ->badge()
                                    ->placeholder('-'),

                                TextEntry::make('ruangan.lantai')
                                    ->label('Lantai')
                                    ->placeholder('-'),
                            ])
                            ->columns([
                                'default' => 1,
                                'lg' => 1,
                            ])
                            ->columnSpan([
                                'default' => 1,
                                'xl' => 1,
                            ]),

                        Section::make('Media')
                            ->icon('heroicon-o-photo')
                            ->description('Foto barang dan QR publik.')
                            ->schema([
                                ImageEntry::make('foto')
                                    ->label('Foto Barang')
                                    ->state(fn ($record): ?string => static::storageFileExists($record->foto)
                                        ? asset('storage/'.$record->foto)
                                        : null)
                                    ->imageSize(180)
                                    ->square()
                                    ->visible(fn ($record): bool => static::storageFileExists($record->foto)),

                                TextEntry::make('foto_status')
                                    ->label('Foto Barang')
                                    ->state('Belum ada foto')
                                    ->badge()
                                    ->color('gray')
                                    ->visible(fn ($record): bool => ! static::storageFileExists($record->foto)),

                                ImageEntry::make('qr_code')
                                    ->label('QR Code')
                                    ->state(fn ($record): ?string => static::storageFileExists($record->qr_code)
                                        ? asset('storage/'.$record->qr_code)
                                        : null)
                                    ->square()
                                    ->imageSize(180)
                                    ->url(fn ($record): ?string => static::storageFileExists($record->qr_code)
                                        ? asset('storage/'.$record->qr_code)
                                        : null)
                                    ->openUrlInNewTab()
                                    ->visible(fn ($record): bool => static::storageFileExists($record->qr_code)),

                                TextEntry::make('qr_code_status')
                                    ->label('QR Code')
                                    ->state('QR belum tersedia')
                                    ->badge()
                                    ->color('gray')
                                    ->visible(fn ($record): bool => ! static::storageFileExists($record->qr_code)),
                            ])
                            ->columns([
                                'default' => 1,
                                'sm' => 2,
                            ])
                            ->columnSpan([
                                'default' => 1,
                                'xl' => 1,
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    protected static function formatRupiah(mixed $state): string
    {
        if (blank($state)) {
            return '-';
        }

        return 'Rp '.number_format((float) $state, 0, ',', '.');
    }

    protected static function storageFileExists(?string $path): bool
    {
        return filled($path) && Storage::disk('public')->exists($path);
    }
}
