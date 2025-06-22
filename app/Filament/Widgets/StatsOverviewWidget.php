<?php

namespace App\Filament\Widgets;

use App\Models\JobPosition;
use App\Models\TimeEntry;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $invalidPositionsCount = TimeEntry::whereNotExists(function ($subQuery) {
            $subQuery->select(DB::raw(1))
                ->from('job_positions')
                ->whereColumn('job_positions.name', 'time_entries.job_title');
        })->count();

        $totalTimeEntries = TimeEntry::count();
        $validPositionsCount = $totalTimeEntries - $invalidPositionsCount;
        $validPositionsPercentage = $totalTimeEntries > 0 ? round(($validPositionsCount / $totalTimeEntries) * 100, 1) : 0;

        return [
            Stat::make('Invalid Positions', $invalidPositionsCount)
                ->description('Time entries with missing job positions')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color($invalidPositionsCount > 0 ? 'danger' : 'success')
                ->chart([7, 12, 8, $invalidPositionsCount])
                ->url(route('filament.admin.resources.time-entries.index', ['tableTab' => 'missing_positions'])),

            Stat::make('Valid Positions', "{$validPositionsPercentage}%")
                ->description("{$validPositionsCount} out of {$totalTimeEntries} entries")
                ->descriptionIcon('heroicon-o-check-circle')
                ->color($validPositionsPercentage >= 90 ? 'success' : ($validPositionsPercentage >= 70 ? 'warning' : 'danger'))
                ->chart([85, 87, 90, $validPositionsPercentage]),

            Stat::make('Total Job Positions', JobPosition::count())
                ->description('Available positions in catalog')
                ->descriptionIcon('heroicon-o-briefcase')
                ->color('primary')
                ->chart([1, 3, 5, JobPosition::count()])
                ->url(route('filament.admin.resources.job-positions.index')),
        ];
    }
}
