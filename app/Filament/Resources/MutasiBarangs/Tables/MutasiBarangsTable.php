<?php

namespace App\Filament\Resources\MutasiBarangs\Tables;

use App\Filament\Resources\MutasiBarangs\MutasiBarangResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MutasiBarangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with([
                'barang:id,kode_barang,nama_barang',
                'ruanganAsal:id,nama_ruangan',
                'ruanganTujuan:id,nama_ruangan',
                'petugas:id,name',
            ]))
            ->columns([
                TextColumn::make('barang.kode_barang')
                    ->label('Kode Barang')
                    ->searchable(),

                TextColumn::make('barang.nama_barang')
                    ->label('Nama Barang')
                    ->searchable(),

                TextColumn::make('ruanganAsal.nama_ruangan')
                    ->label('Ruangan Asal')
                    ->searchable(),

                TextColumn::make('ruanganTujuan.nama_ruangan')
                    ->label('Ruangan Tujuan')
                    ->searchable(),

                TextColumn::make('tanggal_mutasi')
                    ->label('Tanggal Mutasi')
                    ->date()
                    ->sortable(),

                TextColumn::make('kondisi_sebelum')
                    ->label('Kondisi Sebelum')
                    ->badge(),

                TextColumn::make('kondisi_sesudah')
                    ->label('Kondisi Sesudah')
                    ->badge(),

                TextColumn::make('petugas.name')
                    ->label('Dilakukan Oleh')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordUrl(fn ($record): string => MutasiBarangResource::getUrl('view', ['record' => $record], false))
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->visible(function ($record) {
                            $user = Auth::user();

                            return $user?->role === 'admin'
                                || (
                                    $user?->role === 'staff'
                                    && $record->dilakukan_oleh === $user->id
                                );
                        }),

                    DeleteAction::make()
                        ->visible(function ($record) {
                            $user = Auth::user();

                            return $user?->role === 'admin'
                                || (
                                    $user?->role === 'staff'
                                    && $record->dilakukan_oleh === $user->id
                                );
                        }),
                ])
                    ->label('Aksi')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->iconButton()
                    ->visible(function ($record) {
                        $user = Auth::user();

                        return $user?->role === 'admin'
                            || (
                                $user?->role === 'staff'
                                && $record->dilakukan_oleh === $user->id
                            );
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => Auth::user()?->role === 'admin'),
                ]),
            ]);
    }
}
