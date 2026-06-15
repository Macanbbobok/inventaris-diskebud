<?php

namespace App\Filament\Resources\Pemeliharaans\Schemas;

use App\Models\Barang;
use App\Models\RequestKerusakan;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class PemeliharaanForm
{
    private const BARANG_OPTIONS_LIMIT = 50;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('request_kerusakan_id')
                    ->label('Request Kerusakan')
                    ->options(fn () => RequestKerusakan::with('barang')
                        ->whereIn('status', ['diajukan', 'diproses'])
                        ->latest('tanggal_laporan')
                        ->get()
                        ->mapWithKeys(fn (RequestKerusakan $request): array => [
                            $request->id => sprintf(
                                '%s - %s (%s)',
                                $request->barang?->kode_nama ?? '-',
                                $request->tanggal_laporan,
                                ucfirst($request->tingkat_kerusakan),
                            ),
                        ])
                        ->toArray())
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function ($state, $set): void {
                        $request = RequestKerusakan::find($state);

                        if ($request) {
                            $set('barang_ids', [$request->barang_id]);
                            $set('barang_id', $request->barang_id);
                            $set('jenis_pemeliharaan', 'Pemeliharaan kerusakan');
                        }
                    })
                    ->nullable(),

                Select::make('barang_ids')
                    ->label('Barang')
                    ->options(fn (): array => self::getBarangOptions())
                    ->getSearchResultsUsing(fn (string $search): array => self::getBarangOptions($search))
                    ->getOptionLabelsUsing(fn (array $values): array => self::getBarangOptionLabels($values))
                    ->multiple()
                    ->searchable()
                    ->optionsLimit(self::BARANG_OPTIONS_LIMIT)
                    ->required()
                    ->visibleOn('create'),

                Select::make('barang_id')
                    ->label('Barang')
                    ->relationship('barang', 'nama_barang')
                    ->getOptionLabelFromRecordUsing(fn (Barang $record): string => $record->kode_nama)
                    ->searchable(['kode_barang', 'nama_barang'])
                    ->preload()
                    ->required(fn (string $operation): bool => $operation !== 'create')
                    ->hiddenOn('create'),

                DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->required(),

                TextInput::make('jenis_pemeliharaan')
                    ->label('Jenis Pemeliharaan')
                    ->required(),

                Textarea::make('deskripsi')
                    ->label('Deskripsi')
                    ->columnSpanFull(),

                TextInput::make('biaya')
                    ->label('Biaya')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),

                Select::make('kondisi_sesudah')
                    ->label('Kondisi Sesudah')
                    ->options([
                        'baik' => 'Baik',
                        'rusak_ringan' => 'Rusak Ringan',
                        'rusak_berat' => 'Rusak Berat',
                    ])
                    ->required(),

                Forms\Components\Hidden::make('dilakukan_oleh')
                    ->default(fn () => Auth::id()),
            ]);
    }

    private static function getBarangOptions(?string $search = null): array
    {
        return Barang::query()
            ->when(filled($search), function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query
                        ->where('kode_barang', 'like', "%{$search}%")
                        ->orWhere('nama_barang', 'like', "%{$search}%");
                });
            })
            ->orderBy('kode_barang')
            ->limit(self::BARANG_OPTIONS_LIMIT)
            ->get()
            ->mapWithKeys(fn (Barang $record): array => [
                $record->id => $record->kode_nama,
            ])
            ->toArray();
    }

    private static function getBarangOptionLabels(array $values): array
    {
        return Barang::query()
            ->whereKey($values)
            ->orderBy('kode_barang')
            ->get()
            ->mapWithKeys(fn (Barang $record): array => [
                $record->id => $record->kode_nama,
            ])
            ->toArray();
    }
}
