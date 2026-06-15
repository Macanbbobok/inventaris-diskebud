<?php

namespace App\Filament\Resources\Barangs\Tables;

use App\Filament\Resources\Barangs\BarangResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BarangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with([
                'bidang:id,nama_bidang',
                'ruangan:id,nama_ruangan',
            ]))
            ->columns([

                Tables\Columns\ImageColumn::make('foto')
                    ->label('Image')
                    ->state(fn ($record): ?string => static::publicStorageUrl($record->foto))
                    ->defaultImageUrl(fn (): string => static::placeholderImageUrl())
                    ->square()
                    ->size(56),

                Tables\Columns\TextColumn::make('kode_barang')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('nama_barang')
                    ->label('Nama Barang')
                    ->weight(FontWeight::SemiBold)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('bidang.nama_bidang')
                    ->label('Bidang')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('ruangan.nama_ruangan')
                    ->label('Ruangan')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('kondisi')
                    ->label('Kondisi')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'baik' => 'Baik',
                        'rusak_ringan' => 'Rusak Ringan',
                        'rusak_berat' => 'Rusak Berat',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'baik' => 'success',
                        'rusak_ringan' => 'warning',
                        'rusak_berat' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'aktif' => 'Aktif',
                        'dipinjam' => 'Dipinjam',
                        'dihapus' => 'Dihapus',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'aktif' => 'success',
                        'dipinjam' => 'warning',
                        'dihapus' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('harga_perolehan')
                    ->label('Harga')
                    ->formatStateUsing(fn ($state): string => static::formatRupiah($state))
                    ->sortable(),

                Tables\Columns\ImageColumn::make('qr_code')
                    ->label('QR')
                    ->state(fn ($record): ?string => static::publicStorageUrl($record->qr_code))
                    ->defaultImageUrl(fn (): string => static::placeholderImageUrl())
                    ->square()
                    ->size(56)
                    ->url(fn ($record): ?string => static::publicStorageUrl($record->qr_code))
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('tanggal_perolehan')
                    ->label('Tanggal Perolehan')
                    ->date('d M Y')
                    ->sortable(),
            ])

            ->filters([
            Tables\Filters\SelectFilter::make('bidang_id')
                    ->label('Bidang')
                    ->relationship('bidang', 'nama_bidang')
                    ->searchable()
                    ->preload(),
        ])

            ->recordUrl(fn ($record): string => BarangResource::getUrl('view', ['record' => $record]))

            ->recordActions([
            ActionGroup::make([

                    Action::make('lihat_qr')
                        ->label('Lihat QR')
                        ->icon('heroicon-o-qr-code')
                        ->url(fn ($record): ?string => static::publicStorageUrl($record->qr_code))
                        ->openUrlInNewTab()
                        ->visible(fn ($record): bool => static::publicStorageExists($record->qr_code)),

                    Action::make('detail_public')
                        ->label('Detail Publik')
                        ->icon('heroicon-o-eye')
                        ->url(fn ($record) => route('barang.public.show', $record))
                        ->openUrlInNewTab(),

                    EditAction::make()
                        ->visible(fn () => in_array(Auth::user()?->role, ['admin', 'staff'], true)),
            ])
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

    protected static function publicStorageExists(?string $path): bool
    {
        return filled($path) && Storage::disk('public')->exists($path);
    }

    protected static function publicStorageUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL) || str_starts_with($path, 'data:')) {
            return $path;
        }

        return static::publicStorageExists($path)
            ? asset('storage/'.ltrim($path, '/'))
            : null;
    }

    protected static function formatRupiah(mixed $state): string
    {
        if (blank($state)) {
            return '-';
        }

        return 'Rp '.number_format((float) $state, 0, ',', '.');
    }

    protected static function placeholderImageUrl(): string
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="96" height="96" viewBox="0 0 96 96"><rect width="96" height="96" rx="14" fill="#f3f4f6"/><path d="M28 62l12-14 10 10 6-7 12 11" fill="none" stroke="#9ca3af" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/><circle cx="37" cy="35" r="7" fill="#d1d5db"/></svg>';

        return 'data:image/svg+xml;utf8,'.rawurlencode($svg);
    }
}
