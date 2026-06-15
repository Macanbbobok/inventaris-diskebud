<?php

namespace App\Filament\Resources\LaporanInventaris\Tables;

use App\Filament\Resources\LaporanInventaris\LaporanInventarisResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LaporanInventarisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with([
                'pembuat:id,name',
            ]))
            ->columns([
                TextColumn::make('jenis_laporan')
                    ->label('Jenis Laporan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('periode')
                    ->label('Periode')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bulanan' => 'info',
                        'triwulanan' => 'warning',
                        'semesteran' => 'primary',
                        'tahunan' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('bulan')
                    ->label('Bulan')
                    ->sortable(),

                TextColumn::make('tahun')
                    ->label('Tahun')
                    ->sortable(),

                TextColumn::make('pembuat.name')
                    ->label('Dibuat Oleh')
                    ->searchable(),

                TextColumn::make('file_laporan')
                    ->label('File')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordUrl(fn ($record): string => LaporanInventarisResource::getUrl('view', ['record' => $record]))
            ->recordActions([
                ActionGroup::make([
                    Action::make('download')
                        ->label('Download')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->url(fn ($record) => $record->file_laporan
                            ? asset('storage/' . $record->file_laporan)
                            : null)
                        ->openUrlInNewTab()
                        ->visible(fn ($record) => filled($record->file_laporan)),

                    EditAction::make()
                        ->visible(function ($record) {
                            $user = Auth::user();

                            return $user?->role === 'admin'
                                || (
                                    $user?->role === 'staff'
                                    && $record->dibuat_oleh === $user->id
                                );
                        }),

                    DeleteAction::make()
                        ->visible(function ($record) {
                            $user = Auth::user();

                            return $user?->role === 'admin'
                                || (
                                    $user?->role === 'staff'
                                    && $record->dibuat_oleh === $user->id
                                );
                        }),
                ])
                    ->label('Aksi')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->iconButton()
                    ->visible(function ($record) {
                        $user = Auth::user();

                        return filled($record->file_laporan)
                            || $user?->role === 'admin'
                            || (
                                $user?->role === 'staff'
                                && $record->dibuat_oleh === $user->id
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
