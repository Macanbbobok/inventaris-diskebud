<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\RequestKerusakans\RequestKerusakanResource;
use App\Models\RequestKerusakan;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AdminRequestKerusakanWidget extends TableWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = -20;

    public static function canView(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Request Kerusakan Baru')
            ->query(fn (): Builder => RequestKerusakan::query()
                ->with(['barang', 'pelapor'])
                ->where('status', 'diajukan'))
            ->columns([
                TextColumn::make('barang.kode_barang')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('barang.nama_barang')
                    ->label('Barang')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('pelapor.name')
                    ->label('Pelapor')
                    ->placeholder('-')
                    ->searchable(),

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

                TextColumn::make('deskripsi_kerusakan')
                    ->label('Deskripsi')
                    ->limit(70)
                    ->wrap(),

                TextColumn::make('tanggal_laporan')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
            ])
            ->recordUrl(fn (RequestKerusakan $record): string => RequestKerusakanResource::getUrl('view', [
                'record' => $record,
            ], false))
            ->recordActions([
                ActionGroup::make([
                    Action::make('proses')
                        ->label('Proses')
                        ->icon('heroicon-o-clock')
                        ->color('info')
                        ->action(function (RequestKerusakan $record): void {
                            $record->update([
                                'status' => 'diproses',
                            ]);

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
                        ->requiresConfirmation()
                        ->action(function (RequestKerusakan $record): void {
                            $record->update([
                                'status' => 'ditolak',
                            ]);

                            Notification::make()
                                ->title('Request ditolak')
                                ->body($record->barang?->kode_nama . ' tidak dilanjutkan ke pemeliharaan.')
                                ->danger()
                                ->send();
                        }),

                    Action::make('view')
                        ->label('View')
                        ->icon('heroicon-o-eye')
                        ->url(fn (RequestKerusakan $record): string => RequestKerusakanResource::getUrl('view', [
                            'record' => $record,
                        ], false)),
                ])
                    ->label('Aksi')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->iconButton(),
            ])
            ->emptyStateHeading('Tidak ada request baru')
            ->emptyStateDescription('Request yang masih diajukan staff akan muncul di sini.')
            ->defaultSort('created_at', 'desc')
            ->paginated(false)
            ->poll('10s');
    }
}
