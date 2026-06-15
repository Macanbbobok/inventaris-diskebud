<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Bidang;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create'),

                Select::make('role')
                    ->label('Role')
                    ->options([
                        'admin' => 'Admin',
                        'pimpinan' => 'Pimpinan',
                        'staff' => 'Staff',
                    ])
                    ->default('staff')
                    ->required(),

                Select::make('bidang')
                    ->label('Bidang')
                    ->options(fn () => Bidang::query()
                        ->orderBy('nama_bidang')
                        ->pluck('nama_bidang', 'nama_bidang')
                        ->toArray()
                    )
                    ->searchable()
                    ->preload()
                    ->nullable(),
            ]);
    }
}
