<?php

namespace App\Filament\Resources\MutasiBarangs\Schemas;

use App\Models\Barang;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class MutasiBarangForm
{
    private const BARANG_OPTIONS_LIMIT = 50;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('barang_ids')
                    ->label('Barang')
                    ->options(fn (): array => self::getBarangOptions())
                    ->getSearchResultsUsing(fn (string $search): array => self::getBarangOptions($search))
                    ->getOptionLabelsUsing(fn (array $values): array => self::getBarangOptionLabels($values))
                    ->multiple()
                    ->searchable()
                    ->optionsLimit(self::BARANG_OPTIONS_LIMIT)
                    ->live()
                    ->required()
                    ->visibleOn('create'),

                Html::make(fn (Get $get): HtmlString => self::buildOriginPreview(
                    $get('barang_ids'),
                    $get('ruangan_tujuan_id'),
                ))
                    ->columnSpanFull()
                    ->visibleOn('create'),

                Select::make('barang_id')
                    ->label('Barang')
                    ->relationship('barang', 'nama_barang')
                    ->getOptionLabelFromRecordUsing(fn (Barang $record): string => $record->kode_nama)
                    ->searchable(['kode_barang', 'nama_barang'])
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, $set) {
                        $barang = Barang::find($state);

                        if ($barang) {
                            $set('ruangan_asal_id', $barang->ruangan_id);
                            $set('kondisi_sebelum', $barang->kondisi);
                        }
                    })
                    ->required(fn (string $operation): bool => $operation !== 'create')
                    ->hiddenOn('create'),

                Select::make('ruangan_asal_id')
                    ->label('Ruangan Asal')
                    ->relationship('ruanganAsal', 'nama_ruangan')
                    ->disabled()
                    ->dehydrated()
                    ->searchable()
                    ->preload()
                    ->required(fn (string $operation): bool => $operation !== 'create')
                    ->hiddenOn('create'),

                Select::make('ruangan_tujuan_id')
                    ->label('Ruangan Tujuan')
                    ->relationship('ruanganTujuan', 'nama_ruangan')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->different('ruangan_asal_id')
                    ->required(),

                DatePicker::make('tanggal_mutasi')
                    ->label('Tanggal Mutasi')
                    ->required(),

                Textarea::make('alasan')
                    ->label('Alasan')
                    ->columnSpanFull(),

                Select::make('kondisi_sebelum')
                    ->label('Kondisi Sebelum')
                    ->options([
                        'baik' => 'Baik',
                        'rusak_ringan' => 'Rusak Ringan',
                        'rusak_berat' => 'Rusak Berat',
                    ])
                    ->required(fn (string $operation): bool => $operation !== 'create')
                    ->hiddenOn('create'),

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

    private static function buildOriginPreview(mixed $barangIds, mixed $ruanganTujuanId): HtmlString
    {
        $barangIds = collect($barangIds ?? [])
            ->filter()
            ->unique()
            ->values();

        if ($barangIds->isEmpty()) {
            return new HtmlString(<<<'HTML'
                <div style="border: 1px solid #e5e7eb; border-radius: 10px; background: #f9fafb; color: #4b5563; font-size: 14px; line-height: 20px; padding: 12px 14px;">
                    Ruangan asal akan muncul setelah barang dipilih.
                </div>
            HTML);
        }

        $barangs = Barang::with('ruangan')
            ->whereKey($barangIds)
            ->get()
            ->sortBy(fn (Barang $barang): int => $barangIds->search($barang->id))
            ->values();

        $rows = $barangs->map(function (Barang $barang) use ($ruanganTujuanId): string {
            $ruanganAsal = $barang->ruangan?->nama_ruangan ?? '-';
            $kondisi = self::formatCondition($barang->kondisi);
            $status = ((int) $barang->ruangan_id === (int) $ruanganTujuanId && filled($ruanganTujuanId))
                ? '<span style="display: inline-flex; align-items: center; border-radius: 999px; background: #fef3c7; color: #92400e; font-size: 12px; font-weight: 600; padding: 3px 9px; white-space: nowrap;">Tujuan sama</span>'
                : '<span style="display: inline-flex; align-items: center; border-radius: 999px; background: #dcfce7; color: #166534; font-size: 12px; font-weight: 600; padding: 3px 9px; white-space: nowrap;">Siap mutasi</span>';

            return sprintf(
                '<tr>
                    <td style="border-top: 1px solid #e5e7eb; color: #111827; font-weight: 600; padding: 10px 12px; white-space: nowrap;">%s</td>
                    <td style="border-top: 1px solid #e5e7eb; color: #374151; min-width: 180px; padding: 10px 12px;">%s</td>
                    <td style="border-top: 1px solid #e5e7eb; color: #374151; min-width: 160px; padding: 10px 12px;">%s</td>
                    <td style="border-top: 1px solid #e5e7eb; color: #374151; padding: 10px 12px; white-space: nowrap;">%s</td>
                    <td style="border-top: 1px solid #e5e7eb; padding: 10px 12px; white-space: nowrap;">%s</td>
                </tr>',
                e($barang->kode_barang),
                e($barang->nama_barang),
                e($ruanganAsal),
                e($kondisi),
                $status,
            );
        })->implode('');

        return new HtmlString(<<<HTML
            <div style="border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                <div style="background: #f9fafb; color: #111827; font-size: 14px; font-weight: 600; line-height: 20px; padding: 10px 12px;">
                    Ruangan asal barang terpilih
                </div>
                <div style="overflow-x: auto;">
                    <table style="border-collapse: collapse; font-size: 14px; line-height: 20px; min-width: 720px; text-align: left; width: 100%;">
                        <thead style="background: #ffffff; color: #6b7280; font-size: 12px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase;">
                            <tr>
                                <th style="padding: 9px 12px; white-space: nowrap;">Kode</th>
                                <th style="padding: 9px 12px;">Barang</th>
                                <th style="padding: 9px 12px;">Ruangan Asal</th>
                                <th style="padding: 9px 12px; white-space: nowrap;">Kondisi</th>
                                <th style="padding: 9px 12px; white-space: nowrap;">Status</th>
                            </tr>
                        </thead>
                        <tbody>{$rows}</tbody>
                    </table>
                </div>
            </div>
        HTML);
    }

    private static function formatCondition(?string $condition): string
    {
        return match ($condition) {
            'baik' => 'Baik',
            'rusak_ringan' => 'Rusak Ringan',
            'rusak_berat' => 'Rusak Berat',
            default => $condition ?? '-',
        };
    }
}
