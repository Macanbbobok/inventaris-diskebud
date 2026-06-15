<?php

namespace App\Filament\Resources\Pemeliharaans;

use App\Filament\Resources\Pemeliharaans\Pages\CreatePemeliharaan;
use App\Filament\Resources\Pemeliharaans\Pages\EditPemeliharaan;
use App\Filament\Resources\Pemeliharaans\Pages\ListPemeliharaans;
use App\Filament\Resources\Pemeliharaans\Pages\ViewPemeliharaan;
use App\Filament\Resources\Pemeliharaans\Schemas\PemeliharaanForm;
use App\Filament\Resources\Pemeliharaans\Schemas\PemeliharaanInfolist;
use App\Filament\Resources\Pemeliharaans\Tables\PemeliharaansTable;
use App\Models\Pemeliharaan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PemeliharaanResource extends Resource
{
    protected static ?string $model = Pemeliharaan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static string|\UnitEnum|null $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'tanggal';

    protected static ?string $navigationLabel = 'Pemeliharaan';

    protected static ?string $pluralModelLabel = 'Pemeliharaan';

    protected static ?string $modelLabel = 'Pemeliharaan';

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
        return PemeliharaanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PemeliharaanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PemeliharaansTable::configure($table);
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
            'index' => ListPemeliharaans::route('/'),
            'create' => CreatePemeliharaan::route('/create'),
            'view' => ViewPemeliharaan::route('/{record}'),
            'edit' => EditPemeliharaan::route('/{record}/edit'),
        ];
    }
}
