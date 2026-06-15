<?php

namespace App\Filament\Resources\Barangs\Pages;

use App\Filament\Resources\Barangs\BarangResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Storage;

class ViewBarang extends ViewRecord
{
    protected static string $resource = BarangResource::class;

    protected Width | string | null $maxContentWidth = Width::Full;

    public function getTitle(): string
    {
        return $this->record->nama_barang;
    }

    public function getSubheading(): ?string
    {
        return $this->record->kode_barang
            ? 'Kode barang: ' . $this->record->kode_barang
            : null;
    }

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('detail_public')
                    ->label('Detail Publik')
                    ->icon('heroicon-o-eye')
                    ->url(fn () => route('barang.public.show', $this->record))
                    ->openUrlInNewTab(),

                Action::make('lihat_qr')
                    ->label('Lihat QR')
                    ->icon('heroicon-o-qr-code')
                    ->url(fn () => asset('storage/' . $this->record->qr_code))
                    ->openUrlInNewTab()
                    ->visible(fn (): bool => $this->qrCodeExists()),
            ])
                ->label('Aksi')
                ->icon('heroicon-m-ellipsis-vertical')
                ->button()
                ->color('gray'),

            EditAction::make()
                ->label('Edit')
                ->visible(fn (): bool => BarangResource::canEdit($this->record)),
        ];
    }

    protected function qrCodeExists(): bool
    {
        return filled($this->record->qr_code)
            && Storage::disk('public')->exists($this->record->qr_code);
    }
}
