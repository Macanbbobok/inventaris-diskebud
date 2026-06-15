<?php

namespace App\Filament\Resources\RequestKerusakans\Schemas;

use App\Models\Barang;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class RequestKerusakanForm
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
                    ->required(),

                DatePicker::make('tanggal_laporan')
                    ->label('Tanggal Laporan')
                    ->default(now())
                    ->required(),

                Select::make('tingkat_kerusakan')
                    ->label('Tingkat Kerusakan')
                    ->options([
                        'ringan' => 'Ringan',
                        'sedang' => 'Sedang',
                        'berat' => 'Berat',
                    ])
                    ->default('ringan')
                    ->required(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'diajukan' => 'Diajukan',
                        'diproses' => 'Diproses',
                        'selesai' => 'Selesai',
                        'ditolak' => 'Ditolak',
                    ])
                    ->default('diajukan')
                    ->required(),

                Textarea::make('deskripsi_kerusakan')
                    ->label('Deskripsi Kerusakan')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Hidden::make('dilaporkan_oleh')
                    ->default(fn () => Auth::id()),
            ]);
    }
}
