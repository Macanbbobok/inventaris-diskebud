<?php

namespace App\Filament\Resources\RequestKerusakans\Tables;

use App\Filament\Resources\RequestKerusakans\RequestKerusakanResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RequestKerusakansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with([
                'barang:id,kode_barang,nama_barang',
                'pelapor:id,name',
            ]))
            ->columns([
                TextColumn::make('barang.kode_barang')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('barang.nama_barang')
                    ->label('Barang')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tanggal_laporan')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                TextColumn::make('tingkat_kerusakan')
                    ->label('Kerusakan')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'ringan' => 'Ringan',
                        'sedang' => 'Sedang',
                        'berat' => 'Berat',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'ringan' => 'warning',
                        'sedang' => 'danger',
                        'berat' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'diajukan' => 'Diajukan',
                        'diproses' => 'Diproses',
                        'selesai' => 'Selesai',
                        'ditolak' => 'Ditolak',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'diajukan' => 'warning',
                        'diproses' => 'info',
                        'selesai' => 'success',
                        'ditolak' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('pelapor.name')
                    ->label('Pelapor')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordUrl(fn ($record): string => RequestKerusakanResource::getUrl('view', ['record' => $record], false))
            ->recordActions([
                ActionGroup::make([
                    Action::make('proses')
                        ->label('Proses')
                        ->icon('heroicon-o-clock')
                        ->color('info')
                        ->visible(fn ($record): bool => $record->status === 'diajukan' && static::canManage($record))
                        ->action(function ($record): void {
                            $record->update(['status' => 'diproses']);

                            Notification::make()
                                ->title('Request mulai diproses')
                                ->body($record->barang?->kode_nama . ' masuk tahap pemeliharaan.')
                                ->info()
                                ->send();
                        }),

                    Action::make('tolak')
                        ->label('Tolak')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn ($record): bool => in_array($record->status, ['diajukan', 'diproses'], true) && static::canManage($record))
                        ->requiresConfirmation()
                        ->action(function ($record): void {
                            $record->update(['status' => 'ditolak']);

                            Notification::make()
                                ->title('Request ditolak')
                                ->body($record->barang?->kode_nama . ' tidak dilanjutkan ke pemeliharaan.')
                                ->danger()
                                ->send();
                        }),

                    EditAction::make()
                        ->visible(fn ($record): bool => static::canManage($record)),

                    DeleteAction::make()
                        ->visible(fn () => Auth::user()?->role === 'admin'),
                ])
                    ->label('Aksi')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => Auth::user()?->role === 'admin'),
                ]),
            ]);
    }

    private static function canManage($record): bool
    {
        $user = Auth::user();

        return $user?->role === 'admin'
            || (
                $user?->role === 'staff'
                && $record->dilaporkan_oleh === $user->id
            );
    }
}
