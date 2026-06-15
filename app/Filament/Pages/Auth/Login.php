<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    protected string $view = 'filament.auth.login';

    protected static string $layout = 'filament-panels::components.layout.base';

    public function getTitle(): string|Htmlable
    {
        return 'Masuk Dashboard';
    }

    public function getHeading(): string|Htmlable|null
    {
        if (filled($this->userUndertakingMultiFactorAuthentication)) {
            return 'Verifikasi keamanan';
        }

        return 'Masuk Dashboard Inventaris';
    }

    public function getSubheading(): string|Htmlable|null
    {
        if (filled($this->userUndertakingMultiFactorAuthentication)) {
            return 'Selesaikan verifikasi untuk melanjutkan akses.';
        }

        return 'Gunakan akun admin atau petugas untuk mengelola data barang, ruangan, mutasi, dan laporan.';
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email')
            ->email()
            ->required()
            ->autocomplete()
            ->autofocus()
            ->placeholder('nama@diskebud.go.id');
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Password')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->autocomplete('current-password')
            ->required()
            ->placeholder('Masukkan password');
    }

    protected function getRememberFormComponent(): Component
    {
        return Checkbox::make('remember')
            ->label('Ingat sesi login');
    }

    protected function getAuthenticateFormAction(): Action
    {
        return parent::getAuthenticateFormAction()
            ->label('Masuk ke Dashboard')
            ->icon('heroicon-m-arrow-right-on-rectangle');
    }
}
