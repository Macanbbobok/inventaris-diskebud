<?php

namespace App\Filament\Resources\Bidangs\Pages;

use App\Filament\Resources\Bidangs\BidangResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListBidangs extends ListRecords
{
    protected static string $resource = BidangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn () => Auth::user()?->role === 'admin'),
        ];
    }
}
