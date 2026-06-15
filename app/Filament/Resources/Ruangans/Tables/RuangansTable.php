<?php

namespace App\Filament\Resources\Ruangans\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RuangansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with([
                'bidangs:id,nama_bidang',
            ]))
            ->columns([
                TextColumn::make('kode_ruangan')
                    ->searchable(),
                TextColumn::make('nama_ruangan')
                    ->searchable(),
                TextColumn::make('bidangs.nama_bidang')
                    ->label('Bidang')
                    ->badge()
                    ->searchable(),
                TextColumn::make('lantai')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->visible(fn () => Auth::user()?->role === 'admin'),
                ])
                    ->label('Aksi')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->iconButton()
                    ->visible(fn () => Auth::user()?->role === 'admin'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => Auth::user()?->role === 'admin'),
                    ForceDeleteBulkAction::make()
                        ->visible(fn () => Auth::user()?->role === 'admin'),
                    RestoreBulkAction::make()
                        ->visible(fn () => Auth::user()?->role === 'admin'),
                ]),
            ]);
    }
}
