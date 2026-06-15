<?php

namespace App\Filament\Resources\KibBPeralatanMesins\Tables;

use App\Filament\Resources\KibBPeralatanMesins\KibBPeralatanMesinResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class KibBPeralatanMesinsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with([
                'barang:id,kode_barang,nama_barang',
            ]))
            ->columns([
                TextColumn::make('barang.kode_barang')
                    ->label('Kode Barang')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('barang.nama_barang')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('merk_type')
                    ->label('Merk / Type')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('ukuran')
                    ->label('Ukuran')
                    ->searchable(),

                TextColumn::make('bahan')
                    ->label('Bahan')
                    ->searchable(),

                TextColumn::make('no_seri')
                    ->label('No Seri')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordUrl(fn ($record): string => KibBPeralatanMesinResource::getUrl('view', ['record' => $record]))
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->visible(fn () => in_array(Auth::user()?->role, ['admin', 'staff'], true)),

                    DeleteAction::make()
                        ->visible(fn () => in_array(Auth::user()?->role, ['admin', 'staff'], true)),
                ])
                    ->label('Aksi')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->iconButton()
                    ->visible(fn () => in_array(Auth::user()?->role, ['admin', 'staff'], true)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => in_array(Auth::user()?->role, ['admin', 'staff'], true)),
                ]),
            ]);
    }
}
