<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class TipsPool extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Tips Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Tips Pool';

    protected static ?string $navigationLabel = 'Tips Pool';

    protected static string $view = 'filament.pages.tips-pool';
}
