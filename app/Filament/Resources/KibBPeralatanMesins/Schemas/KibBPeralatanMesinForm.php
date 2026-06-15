<?php

namespace App\Filament\Resources\KibBPeralatanMesins\Schemas;

use App\Models\Barang;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class KibBPeralatanMesinForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('barang_id')
                    ->label('Barang')
                    ->relationship('barang', 'nama_barang')
                    ->getOptionLabelFromRecordUsing(fn (Barang $record): string => $record->kode_nama)
                    ->searchable(['kode_barang', 'nama_barang'])
                    ->preload()
                    ->required()
                    ->helperText('Pilih barang yang akan diberikan detail KIB B.'),

                TextInput::make('merk_type')
                    ->label('Merk / Type')
                    ->placeholder('Contoh: ASUS VivoBook 14')
                    ->required(),

                TextInput::make('ukuran')
                    ->label('Ukuran')
                    ->placeholder('Contoh: 14 inch, A4, 1 PK'),

                TextInput::make('bahan')
                    ->label('Bahan')
                    ->placeholder('Contoh: Plastik, Besi, Aluminium'),

                TextInput::make('no_seri')
                    ->label('No Seri')
                    ->placeholder('Contoh: ASUS-88271'),

                Textarea::make('spesifikasi')
                    ->label('Spesifikasi')
                    ->placeholder('Contoh: Ryzen 5, RAM 8GB, SSD 512GB')
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }
}
