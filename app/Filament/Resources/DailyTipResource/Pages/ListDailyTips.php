<?php

namespace App\Filament\Resources\DailyTipResource\Pages;

use App\Filament\Resources\DailyTipResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDailyTips extends ListRecords
{
    protected static string $resource = DailyTipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
