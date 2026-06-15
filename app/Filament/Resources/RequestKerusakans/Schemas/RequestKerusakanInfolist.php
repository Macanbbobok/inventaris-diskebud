<?php

namespace App\Filament\Resources\RequestKerusakans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class RequestKerusakanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ringkasan Request')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->description('Laporan kerusakan barang dan status tindak lanjutnya.')
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

                        TextEntry::make('tanggal_laporan')
                            ->label('Tanggal Laporan')
                            ->date('d M Y')
                            ->placeholder('-'),

                        TextEntry::make('tingkat_kerusakan')
                            ->label('Tingkat Kerusakan')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => self::formatDamageLevel($state))
                            ->color(fn (?string $state): string => self::damageColor($state)),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => self::formatStatus($state))
                            ->color(fn (?string $state): string => self::statusColor($state)),
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
                        Section::make('Detail Kerusakan')
                            ->icon('heroicon-o-document-text')
                            ->description('Catatan kerusakan yang dilaporkan oleh pengguna.')
                            ->schema([
                                TextEntry::make('deskripsi_kerusakan')
                                    ->label('Deskripsi Kerusakan')
                                    ->placeholder('-')
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Pelapor & Tindak Lanjut')
                            ->icon('heroicon-o-user-circle')
                            ->description('Pelapor dan hubungan dengan data pemeliharaan.')
                            ->schema([
                                TextEntry::make('pelapor.name')
                                    ->label('Dilaporkan Oleh')
                                    ->placeholder('-'),

                                TextEntry::make('pemeliharaan.tanggal')
                                    ->label('Tanggal Pemeliharaan')
                                    ->date('d M Y')
                                    ->placeholder('Belum ada pemeliharaan'),

                                TextEntry::make('pemeliharaan.jenis_pemeliharaan')
                                    ->label('Jenis Pemeliharaan')
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

    private static function formatStatus(?string $status): string
    {
        return match ($status) {
            'diajukan' => 'Diajukan',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            default => $status ?? '-',
        };
    }

    private static function statusColor(?string $status): string
    {
        return match ($status) {
            'diajukan' => 'warning',
            'diproses' => 'info',
            'selesai' => 'success',
            'ditolak' => 'danger',
            default => 'gray',
        };
    }
}
