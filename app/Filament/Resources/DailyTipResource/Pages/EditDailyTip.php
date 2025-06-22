<?php

namespace App\Filament\Resources\DailyTipResource\Pages;

use App\Filament\Resources\DailyTipResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditDailyTip extends EditRecord
{
    protected static string $resource = DailyTipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Daily Tip Deleted Successfully')
                        ->body('The daily tip has been removed from the system.')
                        ->icon('heroicon-o-trash')
                ),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Daily Tip Updated Successfully')
            ->body('The daily tip changes have been saved.')
            ->icon('heroicon-o-pencil-square')
            ->send();
    }
}
