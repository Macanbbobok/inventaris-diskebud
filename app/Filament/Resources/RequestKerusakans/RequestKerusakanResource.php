<?php

namespace App\Filament\Resources\RequestKerusakans;

use App\Filament\Resources\RequestKerusakans\Pages\CreateRequestKerusakan;
use App\Filament\Resources\RequestKerusakans\Pages\EditRequestKerusakan;
use App\Filament\Resources\RequestKerusakans\Pages\ListRequestKerusakans;
use App\Filament\Resources\RequestKerusakans\Pages\ViewRequestKerusakan;
use App\Filament\Resources\RequestKerusakans\Schemas\RequestKerusakanForm;
use App\Filament\Resources\RequestKerusakans\Schemas\RequestKerusakanInfolist;
use App\Filament\Resources\RequestKerusakans\Tables\RequestKerusakansTable;
use App\Models\RequestKerusakan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class RequestKerusakanResource extends Resource
{
    protected static ?string $model = RequestKerusakan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static string|\UnitEnum|null $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'tanggal_laporan';

    protected static ?string $navigationLabel = 'Request Kerusakan';

    protected static ?string $pluralModelLabel = 'Request Kerusakan';

    protected static ?string $modelLabel = 'Request Kerusakan';

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
                && $record->dilaporkan_oleh === $user->id
            );
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::user()?->role === 'admin';
    }

    public static function form(Schema $schema): Schema
    {
        return RequestKerusakanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RequestKerusakanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RequestKerusakansTable::configure($table);
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
            'index' => ListRequestKerusakans::route('/'),
            'create' => CreateRequestKerusakan::route('/create'),
            'view' => ViewRequestKerusakan::route('/{record}'),
            'edit' => EditRequestKerusakan::route('/{record}/edit'),
        ];
    }
}
