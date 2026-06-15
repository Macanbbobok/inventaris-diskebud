<?php

namespace App\Filament\Resources\LaporanInventaris;

use App\Filament\Resources\LaporanInventaris\Pages\CreateLaporanInventaris;
use App\Filament\Resources\LaporanInventaris\Pages\EditLaporanInventaris;
use App\Filament\Resources\LaporanInventaris\Pages\ListLaporanInventaris;
use App\Filament\Resources\LaporanInventaris\Pages\ViewLaporanInventaris;
use App\Filament\Resources\LaporanInventaris\Schemas\LaporanInventarisForm;
use App\Filament\Resources\LaporanInventaris\Schemas\LaporanInventarisInfolist;
use App\Filament\Resources\LaporanInventaris\Tables\LaporanInventarisTable;
use App\Models\LaporanInventaris;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LaporanInventarisResource extends Resource
{
    protected static ?string $model = LaporanInventaris::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'jenis_laporan';

    protected static ?string $navigationLabel = 'Laporan Inventaris';

    protected static ?string $pluralModelLabel = 'Laporan Inventaris';

    protected static ?string $modelLabel = 'Laporan Inventaris';

    public static function canCreate(): bool
    {
        return in_array(Auth::user()?->role, ['admin', 'staff'], true);
    }

    public static function canEdit(Model $record): bool
    {
        $user = Auth::user();

        return $user?->role === 'admin'
            || (
                $user?->role === 'staff'
                && $record->dibuat_oleh === $user->id
            );
    }

    public static function canDelete(Model $record): bool
    {
        $user = Auth::user();

        return $user?->role === 'admin'
            || (
                $user?->role === 'staff'
                && $record->dibuat_oleh === $user->id
            );
    }

    public static function form(Schema $schema): Schema
    {
        return LaporanInventarisForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LaporanInventarisInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LaporanInventarisTable::configure($table);
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
            'index' => ListLaporanInventaris::route('/'),
            'create' => CreateLaporanInventaris::route('/create'),
            'view' => ViewLaporanInventaris::route('/{record}'),
            'edit' => EditLaporanInventaris::route('/{record}/edit'),
        ];
    }
}
