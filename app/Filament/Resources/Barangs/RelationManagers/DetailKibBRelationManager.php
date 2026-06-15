<?php

namespace App\Filament\Resources\Barangs\RelationManagers;

use App\Filament\Resources\KibBPeralatanMesins\KibBPeralatanMesinResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class DetailKibBRelationManager extends RelationManager
{
    protected static string $relationship = 'detailKibB';

    protected static ?string $relatedResource = KibBPeralatanMesinResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make()
                    ->visible(fn () => in_array(Auth::user()?->role, ['admin', 'staff'], true)
                        && $this->getOwnerRecord()->detailKibB === null),
            ]);
    }
}
