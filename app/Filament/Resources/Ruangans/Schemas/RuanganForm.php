<?php

namespace App\Filament\Resources\Ruangans\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RuanganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode_ruangan')
                    ->required(),
                TextInput::make('nama_ruangan')
                    ->required(),
                Select::make('bidangs')
                    ->label('Bidang')
                    ->relationship('bidangs', 'nama_bidang')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                TextInput::make('lantai'),
            ]);
    }
}
