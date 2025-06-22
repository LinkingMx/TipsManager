<?php

namespace App\Filament\Widgets;

use App\Models\JobPosition;
use App\Models\TimeEntry;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class InvalidPositionsWidget extends BaseWidget
{
    protected static ?string $heading = 'Time Entries with Invalid Positions';

    protected static ?string $description = 'Entries that have positions not found in the Job Positions catalog';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TimeEntry::query()
                    ->whereNotExists(function ($subQuery) {
                        $subQuery->select(DB::raw(1))
                            ->from('job_positions')
                            ->whereColumn('job_positions.name', 'time_entries.job_title');
                    })
                    ->orderBy('in_date', 'desc')
            )
            ->columns([
                Tables\Columns\IconColumn::make('status')
                    ->label('Status')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->tooltip('Position not found in Job Positions catalog')
                    ->width('60px'),

                Tables\Columns\TextColumn::make('employee_name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('job_title')
                    ->label('Invalid Position')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('danger')
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('in_date')
                    ->label('Clock In')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('location')
                    ->label('Location')
                    ->searchable()
                    ->limit(20)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();

                        return strlen($state) > 20 ? $state : null;
                    }),

                Tables\Columns\TextColumn::make('total_hours')
                    ->label('Hours')
                    ->numeric(2)
                    ->alignEnd(),
            ])
            ->actions([
                Tables\Actions\Action::make('edit')
                    ->label('Edit Entry')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn (TimeEntry $record): string => route('filament.admin.resources.time-entries.edit', $record))
                    ->openUrlInNewTab(false),

                Tables\Actions\Action::make('create_position')
                    ->label('Create Position')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->action(function (TimeEntry $record) {
                        // Create the job position if it doesn't exist
                        JobPosition::firstOrCreate(
                            ['name' => $record->job_title],
                            ['points' => 1] // Default points, can be adjusted later
                        );

                        // Refresh the widget
                        $this->dispatch('refresh');

                        // Show notification
                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Position Created Successfully')
                            ->body("Job position '{$record->job_title}' has been added to the catalog.")
                            ->icon('heroicon-o-check-circle')
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Create Job Position')
                    ->modalDescription(fn (TimeEntry $record) => "Create job position '{$record->job_title}' in the Job Positions catalog?")
                    ->modalSubmitActionLabel('Create Position'),
            ])
            ->emptyStateHeading('No Invalid Positions Found')
            ->emptyStateDescription('All time entries have valid positions in the Job Positions catalog.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(10);
    }

    protected function getTableHeading(): ?string
    {
        $count = TimeEntry::whereNotExists(function ($subQuery) {
            $subQuery->select(DB::raw(1))
                ->from('job_positions')
                ->whereColumn('job_positions.name', 'time_entries.job_title');
        })->count();

        return "Time Entries with Invalid Positions ({$count})";
    }
}
