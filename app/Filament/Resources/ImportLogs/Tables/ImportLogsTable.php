<?php

namespace App\Filament\Resources\ImportLogs\Tables;

use App\Filament\Resources\ImportLogs\ImportLogResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ImportLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with([
                'uploader:id,name',
            ]))
            ->columns([
                TextColumn::make('nama_file')
                    ->label('Nama File')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tipe_import')
                    ->label('Tipe Import')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('jenis_kib')
                    ->label('Jenis KIB')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('total_baris')
                    ->label('Total Baris')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('berhasil')
                    ->label('Berhasil')
                    ->numeric()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('gagal')
                    ->label('Gagal')
                    ->numeric()
                    ->color('danger')
                    ->sortable(),

                TextColumn::make('duplikat')
                    ->label('Duplikat')
                    ->numeric()
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('status')
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

                TextColumn::make('uploader.name')
                    ->label('Diupload Oleh')
                    ->searchable(),

                TextColumn::make('waktu_selesai')
                    ->label('Waktu Selesai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('path_file')
                    ->label('Path File')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Tanggal Upload')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordUrl(fn ($record): string => ImportLogResource::getUrl('view', ['record' => $record]))
            ->recordActions([
                ActionGroup::make([
                    DeleteAction::make()
                        ->visible(function ($record) {
                            $user = Auth::user();

                            return $user?->role === 'admin'
                                || (
                                    $user?->role === 'staff'
                                    && $record->diupload_oleh === $user->id
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
                                && $record->diupload_oleh === $user->id
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
