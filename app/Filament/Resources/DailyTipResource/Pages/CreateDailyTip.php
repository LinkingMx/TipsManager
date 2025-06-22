<?php

namespace App\Filament\Resources\DailyTipResource\Pages;

use App\Filament\Resources\DailyTipResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateDailyTip extends CreateRecord
{
    protected static string $resource = DailyTipResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Daily Tip Created Successfully')
            ->body('The daily tip has been created and is now available in the system.')
            ->icon('heroicon-o-currency-dollar')
            ->send();
    }
}
