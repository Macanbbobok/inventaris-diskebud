<?php

namespace App\Filament\Resources\MutasiBarangs;

use App\Filament\Resources\MutasiBarangs\Pages\CreateMutasiBarang;
use App\Filament\Resources\MutasiBarangs\Pages\EditMutasiBarang;
use App\Filament\Resources\MutasiBarangs\Pages\ListMutasiBarangs;
use App\Filament\Resources\MutasiBarangs\Pages\ViewMutasiBarang;
use App\Filament\Resources\MutasiBarangs\Schemas\MutasiBarangForm;
use App\Filament\Resources\MutasiBarangs\Schemas\MutasiBarangInfolist;
use App\Filament\Resources\MutasiBarangs\Tables\MutasiBarangsTable;
use App\Models\MutasiBarang;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MutasiBarangResource extends Resource
{
    protected static ?string $model = MutasiBarang::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static string|\UnitEnum|null $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'tanggal_mutasi';

    protected static ?string $navigationLabel = 'Mutasi Barang';

    protected static ?string $pluralModelLabel = 'Mutasi Barang';

    protected static ?string $modelLabel = 'Mutasi Barang';

    public static function canCreate(): bool
    {
        return in_array(Auth::user()?->role, ['admin', 'staff']);
    }

    public static function canEdit(Model $record): bool
    {
        $user = Auth::user();

        return $user?->role === 'admin'
            || (
                $user?->role === 'staff'
                && $record->dilakukan_oleh === $user->id
            );
    }

    public static function canDelete(Model $record): bool
    {
        $user = Auth::user();

        return $user?->role === 'admin'
            || (
                $user?->role === 'staff'
                && $record->dilakukan_oleh === $user->id
            );
    }

    public static function form(Schema $schema): Schema
    {
        return MutasiBarangForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MutasiBarangInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MutasiBarangsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMutasiBarangs::route('/'),
            'create' => CreateMutasiBarang::route('/create'),
            'view' => ViewMutasiBarang::route('/{record}'),
            'edit' => EditMutasiBarang::route('/{record}/edit'),
        ];
    }
}
