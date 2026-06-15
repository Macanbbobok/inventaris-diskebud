<?php

namespace App\Filament\Resources\ImportLogs\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ImportLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ringkasan Import')
                    ->schema([
                        TextEntry::make('nama_file')
                            ->label('Nama File'),

                        TextEntry::make('tipe_import')
                            ->label('Tipe Import')
                            ->placeholder('-'),

                        TextEntry::make('jenis_kib')
                            ->label('Jenis KIB')
                            ->badge()
                            ->color('primary'),

                        TextEntry::make('uploader.name')
                            ->label('Diupload Oleh')
                            ->placeholder('-'),

                        TextEntry::make('path_file')
                            ->label('Path File')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Section::make('Hasil Import')
                    ->schema([
                        TextEntry::make('total_baris')
                            ->label('Total Baris'),

                        TextEntry::make('berhasil')
                            ->label('Berhasil')
                            ->badge()
                            ->color('success'),

                        TextEntry::make('gagal')
                            ->label('Gagal')
                            ->badge()
                            ->color('danger'),

                        TextEntry::make('duplikat')
                            ->label('Duplikat')
                            ->badge()
                            ->color('warning'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'sukses' => 'Sukses',
                                'sebagian' => 'Sebagian',
                                'gagal' => 'Gagal',
                                default => $state,
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'sukses' => 'success',
                                'sebagian' => 'warning',
                                'gagal' => 'danger',
                                default => 'gray',
                            }),

                        TextEntry::make('waktu_selesai')
                            ->label('Waktu Selesai')
                            ->dateTime('d M Y H:i')
                            ->placeholder('-'),
                    ])
                    ->columns(3),

                Section::make('Informasi Error')
                    ->schema([
                        TextEntry::make('catatan_error')
                            ->label('Catatan Error')
                            ->placeholder('Tidak ada catatan error.')
                            ->columnSpanFull(),

                        RepeatableEntry::make('detail_error')
                            ->label('Detail Error')
                            ->schema([
                                TextEntry::make('baris')
                                    ->label('Baris'),

                                TextEntry::make('tipe')
                                    ->label('Tipe')
                                    ->badge()
                                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                                        'duplikat' => 'Duplikat',
                                        'gagal' => 'Gagal',
                                        default => $state ?? '-',
                                    })
                                    ->color(fn (?string $state): string => match ($state) {
                                        'duplikat' => 'warning',
                                        'gagal' => 'danger',
                                        default => 'gray',
                                    }),

                                TextEntry::make('pesan')
                                    ->label('Pesan')
                                    ->columnSpan(2),

                                TextEntry::make('data.kode_barang')
                                    ->label('Kode Barang')
                                    ->placeholder('-'),

                                TextEntry::make('data.nama_barang')
                                    ->label('Nama Barang')
                                    ->placeholder('-'),

                                TextEntry::make('data.bidang')
                                    ->label('Bidang')
                                    ->placeholder('-'),

                                TextEntry::make('data.ruangan')
                                    ->label('Ruangan')
                                    ->placeholder('-'),
                            ])
                            ->columns(4)
                            ->placeholder('Tidak ada detail error.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
