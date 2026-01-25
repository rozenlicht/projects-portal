<?php

namespace App\Filament\Pages\Auth;

use App\Helpers\SamlHelper;
use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    public function getSubheading(): string | Htmlable | null
    {
        if (SamlHelper::isEnabled()) {
            return 'Sign in to your account using your password or SURF Conext SSO';
        }
        
        return 'Sign in to your account';
    }

    protected function getFormActions(): array
    {
        $actions = [
            $this->getAuthenticateFormAction(),
        ];
        
        if (SamlHelper::isEnabled()) {
            $actions[] = $this->getSamlLoginAction();
        }
        
        return $actions;
    }

    protected function getSamlLoginAction(): \Filament\Actions\Action
    {
        return \Filament\Actions\Action::make('samlLogin')
            ->label('Sign in with SURF Conext')
            ->color('primary')
            ->outlined()
            ->url(route('saml.login', ['guard' => 'web', 'return' => '/admin']))
            ->icon('heroicon-o-arrow-right-on-rectangle');
    }
}
