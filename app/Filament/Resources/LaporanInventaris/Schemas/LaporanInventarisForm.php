<?php

namespace App\Filament\Resources\LaporanInventaris\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class LaporanInventarisForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('jenis_laporan')
                    ->label('Jenis Laporan')
                    ->options([
                        'barang' => 'Laporan Barang',
                        'kib_b' => 'Laporan KIB B Peralatan Mesin',
                        'mutasi' => 'Laporan Mutasi Barang',
                        'pemeliharaan' => 'Laporan Pemeliharaan',
                        'gabungan' => 'Laporan Gabungan',
                    ])
                    ->required(),

                Select::make('periode')
                    ->label('Periode')
                    ->options([
                        'bulanan' => 'Bulanan',
                        'triwulanan' => 'Triwulanan',
                        'semesteran' => 'Semesteran',
                        'tahunan' => 'Tahunan',
                    ])
                    ->default('bulanan')
                    ->live()
                    ->required(),

                TextInput::make('bulan')
                    ->label('Bulan')
                    ->numeric()
                    ->default(now()->month)
                    ->minValue(1)
                    ->maxValue(12)
                    ->visible(fn ($get) => $get('periode') !== 'tahunan')
                    ->required(fn ($get) => $get('periode') === 'bulanan')
                    ->helperText('Otomatis mengikuti bulan saat ini. Tidak dipakai untuk laporan tahunan.'),

                TextInput::make('tahun')
                    ->label('Tahun')
                    ->numeric()
                    ->default(now()->year)
                    ->required(),

                TextInput::make('file_laporan')
                    ->label('File Laporan')
                    ->readOnly()
                    ->dehydrated(false)
                    ->helperText('File akan dibuat otomatis setelah laporan disimpan.'),

                Select::make('dibuat_oleh')
                    ->label('Dibuat Oleh')
                    ->relationship('pembuat', 'name')
                    ->default(fn () => Auth::id())
                    ->disabled()
                    ->dehydrated(),
            ]);
    }
}
