<?php

namespace App\Filament\Resources\Bidangs;

use App\Filament\Resources\Bidangs\Pages\CreateBidang;
use App\Filament\Resources\Bidangs\Pages\EditBidang;
use App\Filament\Resources\Bidangs\Pages\ListBidangs;
use App\Filament\Resources\Bidangs\Schemas\BidangForm;
use App\Filament\Resources\Bidangs\Tables\BidangsTable;
use App\Models\Bidang;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BidangResource extends Resource
{
    protected static ?string $model = Bidang::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|\UnitEnum|null $navigationGroup = 'Data Master';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'nama_bidang';

    public static function getModelLabel(): string
    {
        return 'Bidang';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Bidang';
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::user()?->role === 'admin';
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::user()?->role === 'admin';
    }

    public static function form(Schema $schema): Schema
    {
        return BidangForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BidangsTable::configure($table);
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
            'index' => ListBidangs::route('/'),
            'create' => CreateBidang::route('/create'),
            'edit' => EditBidang::route('/{record}/edit'),
        ];
    }
}
