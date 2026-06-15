<?php

namespace App\Filament\Resources\ImportLogs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ImportLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_file')
                    ->label('Nama File')
                    ->required(),

                TextInput::make('tipe_import')
                    ->label('Tipe Import'),

                Select::make('jenis_kib')
                    ->label('Jenis KIB')
                    ->options(['B' => 'B'])
                    ->default('B')
                    ->required(),

                TextInput::make('total_baris')
                    ->label('Total Baris')
                    ->required()
                    ->numeric()
                    ->default(0),

                TextInput::make('berhasil')
                    ->label('Berhasil')
                    ->required()
                    ->numeric()
                    ->default(0),

                TextInput::make('gagal')
                    ->label('Gagal')
                    ->required()
                    ->numeric()
                    ->default(0),

                TextInput::make('duplikat')
                    ->label('Duplikat')
                    ->required()
                    ->numeric()
                    ->default(0),

                Select::make('status')
                    ->label('Status')
                    ->options(['sukses' => 'Sukses', 'sebagian' => 'Sebagian', 'gagal' => 'Gagal'])
                    ->default('sukses')
                    ->required(),

                Textarea::make('catatan_error')
                    ->label('Catatan Error')
                    ->columnSpanFull(),

                Textarea::make('detail_error')
                    ->label('Detail Error')
                    ->rows(6)
                    ->columnSpanFull(),

                TextInput::make('path_file')
                    ->label('Path File'),

                DateTimePicker::make('waktu_selesai')
                    ->label('Waktu Selesai'),

                Select::make('diupload_oleh')
                    ->label('Diupload Oleh')
                    ->relationship('uploader', 'name')
                    ->searchable()
                    ->preload(),
            ]);
    }
}
