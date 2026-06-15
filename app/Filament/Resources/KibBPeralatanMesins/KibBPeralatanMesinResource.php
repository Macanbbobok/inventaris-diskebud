<?php

namespace App\Filament\Resources\KibBPeralatanMesins;
use App\Filament\Resources\KibBPeralatanMesins\Pages\CreateKibBPeralatanMesin;
use App\Filament\Resources\KibBPeralatanMesins\Pages\EditKibBPeralatanMesin;
use App\Filament\Resources\KibBPeralatanMesins\Pages\ListKibBPeralatanMesins;
use App\Filament\Resources\KibBPeralatanMesins\Pages\ViewKibBPeralatanMesin;
use App\Filament\Resources\KibBPeralatanMesins\Schemas\KibBPeralatanMesinForm;
use App\Filament\Resources\KibBPeralatanMesins\Schemas\KibBPeralatanMesinInfolist;
use App\Filament\Resources\KibBPeralatanMesins\Tables\KibBPeralatanMesinsTable;
use App\Models\KibBPeralatanMesin;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class KibBPeralatanMesinResource extends Resource
{
    protected static ?string $model = KibBPeralatanMesin::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|\UnitEnum|null $navigationGroup = 'Inventaris';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'merk_type';

    protected static ?string $navigationLabel = 'Detail KIB B';

    protected static ?string $pluralModelLabel = 'Detail KIB B';

    protected static ?string $modelLabel = 'Detail KIB B';

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
        return in_array(Auth::user()?->role, ['admin', 'staff'], true);
    }

    public static function form(Schema $schema): Schema
    {
        return KibBPeralatanMesinForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KibBPeralatanMesinInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KibBPeralatanMesinsTable::configure($table);
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
            'index' => ListKibBPeralatanMesins::route('/'),
            'create' => CreateKibBPeralatanMesin::route('/create'),
            'view' => ViewKibBPeralatanMesin::route('/{record}'),
            'edit' => EditKibBPeralatanMesin::route('/{record}/edit'),
        ];
    }
}
