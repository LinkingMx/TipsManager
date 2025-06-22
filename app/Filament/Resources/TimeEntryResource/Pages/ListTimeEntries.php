<?php

namespace App\Filament\Resources\TimeEntryResource\Pages;

use App\Filament\Resources\TimeEntryResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ListTimeEntries extends ListRecords
{
    protected static string $resource = TimeEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Entries')
                ->icon('heroicon-o-squares-2x2')
                ->badge(fn () => $this->getResource()::getEloquentQuery()->count()),

            'valid_positions' => Tab::make('Valid Positions')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('job_positions')
                        ->whereColumn('job_positions.name', 'time_entries.job_title');
                })
                )
                ->badge(fn () => $this->getResource()::getEloquentQuery()
                    ->whereExists(function ($subQuery) {
                        $subQuery->select(DB::raw(1))
                            ->from('job_positions')
                            ->whereColumn('job_positions.name', 'time_entries.job_title');
                    })->count()
                ),

            'missing_positions' => Tab::make('Missing Positions')
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('job_positions')
                        ->whereColumn('job_positions.name', 'time_entries.job_title');
                })
                )
                ->badge(fn () => $this->getResource()::getEloquentQuery()
                    ->whereNotExists(function ($subQuery) {
                        $subQuery->select(DB::raw(1))
                            ->from('job_positions')
                            ->whereColumn('job_positions.name', 'time_entries.job_title');
                    })->count()
                ),
        ];
    }
}
