<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

class HelpAndContact extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected string $view = 'filament.pages.help-and-contact';

    protected static ?string $navigationLabel = 'Help & Contact';

    protected static ?int $navigationSort = 100;

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ?? 'Help & Contact';
    }

    public function getTitle(): string | Htmlable
    {
        return 'Help & Contact';
    } 
}

