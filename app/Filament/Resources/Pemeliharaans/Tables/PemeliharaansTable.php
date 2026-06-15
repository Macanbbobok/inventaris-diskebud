<?php

namespace App\Filament\Resources\Pemeliharaans\Tables;

use App\Filament\Resources\Pemeliharaans\PemeliharaanResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PemeliharaansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with([
                'barang:id,kode_barang,nama_barang',
                'requestKerusakan:id,status',
                'petugas:id,name',
            ]))
            ->columns([
                TextColumn::make('barang.kode_barang')
                    ->label('Kode Barang')
                    ->searchable(),

                TextColumn::make('barang.nama_barang')
                    ->label('Nama Barang')
                    ->searchable(),

                TextColumn::make('requestKerusakan.status')
                    ->label('Request')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'diajukan' => 'Diajukan',
                        'diproses' => 'Diproses',
                        'selesai' => 'Selesai',
                        'ditolak' => 'Ditolak',
                        default => '-',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'diajukan' => 'warning',
                        'diproses' => 'info',
                        'selesai' => 'success',
                        'ditolak' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                TextColumn::make('jenis_pemeliharaan')
                    ->label('Jenis Pemeliharaan')
                    ->searchable(),

                TextColumn::make('biaya')
                    ->label('Biaya')
                    ->money('IDR')
                    ->sortable(),

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
            ->recordUrl(fn ($record): string => PemeliharaanResource::getUrl('view', ['record' => $record], false))
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
