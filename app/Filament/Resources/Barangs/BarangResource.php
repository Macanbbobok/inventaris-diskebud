<?php

namespace App\Filament\Resources\Barangs;

use App\Filament\Resources\Barangs\Pages\CreateBarang;
use App\Filament\Resources\Barangs\Pages\EditBarang;
use App\Filament\Resources\Barangs\Pages\ListBarangs;
use App\Filament\Resources\Barangs\Pages\ViewBarang;
use App\Filament\Resources\Barangs\Schemas\BarangInfolist;
use App\Filament\Resources\Barangs\Tables\BarangsTable;
use App\Models\Barang;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static string|\UnitEnum|null $navigationGroup = 'Inventaris';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'nama_barang';

    protected static ?string $navigationLabel = 'Barang';

    protected static ?string $pluralModelLabel = 'Barang';

    protected static ?string $modelLabel = 'Barang';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('kode_barang')
                    ->default(function () {
                        $last = Barang::latest('id')->first();

                        $number = $last ? ((int) str_replace('BRG-', '', $last->kode_barang)) + 1 : 1;

                        return 'BRG-'.str_pad($number, 4, '0', STR_PAD_LEFT);
                    })
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->readOnly(),

                Forms\Components\TextInput::make('nama_barang')
                    ->required(),

                Forms\Components\Select::make('bidang_id')
                    ->label('Bidang Penanggung Jawab')
                    ->relationship('bidang', 'nama_bidang')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('ruangan_id')
                    ->label('Ruangan')
                    ->relationship('ruangan', 'nama_ruangan')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('tahun_perolehan')
                    ->label('Tahun Perolehan')
                    ->numeric()
                    ->default(now()->year)
                    ->readOnly()
                    ->helperText('Otomatis mengikuti tanggal perolehan.'),

                Forms\Components\DatePicker::make('tanggal_perolehan')
                    ->label('Tanggal Perolehan')
                    ->default(now())
                    ->live()
                    ->afterStateUpdated(function ($state, $set): void {
                        $set('tahun_perolehan', filled($state) ? Carbon::parse($state)->year : null);
                    })
                    ->required()
                    ->helperText('Dipakai sebagai dasar periode laporan otomatis.'),

                Forms\Components\Select::make('kondisi')
                    ->options([
                        'baik' => 'Baik',
                        'rusak_ringan' => 'Rusak Ringan',
                        'rusak_berat' => 'Rusak Berat',
                    ])
                    ->default('baik')
                    ->required(),

                Forms\Components\Select::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'dipinjam' => 'Dipinjam',
                        'dihapus' => 'Dihapus',
                    ])
                    ->default('aktif')
                    ->required(),

                Forms\Components\TextInput::make('harga_perolehan')
                    ->numeric()
                    ->prefix('Rp'),

                Forms\Components\FileUpload::make('foto')
                    ->image()
                    ->disk('public')
                    ->directory('barang'),

                Forms\Components\Hidden::make('created_by')
                    ->default(fn () => Auth::id()),
            ]);
    }

    public static function canCreate(): bool
    {
        return in_array(Auth::user()?->role, ['admin', 'staff'], true);
    }

    public static function canEdit(Model $record): bool
    {
        return in_array(Auth::user()?->role, ['admin', 'staff'], true);
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::user()?->role === 'admin';
    }

    public static function table(Table $table): Table
    {
        return BarangsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BarangInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DetailKibBRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBarangs::route('/'),
            'create' => CreateBarang::route('/create'),
            'view' => ViewBarang::route('/{record}'),
            'edit' => EditBarang::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
