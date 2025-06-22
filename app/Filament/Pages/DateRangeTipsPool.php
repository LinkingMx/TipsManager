<?php

namespace App\Filament\Pages;

use App\Models\DailyTip;
use App\Models\JobPosition;
use App\Models\TimeEntry;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class DateRangeTipsPool extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Tips Management';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Date Range Tips Pool';

    protected static ?string $navigationLabel = 'Date Range Tips Pool';

    protected static string $view = 'filament.pages.date-range-tips-pool';

    public ?string $startDate = null;

    public ?string $endDate = null;

    public array $tipsData = [];

    public array $summary = [];

    public array $dailyBreakdown = [];

    public function mount(): void
    {
        $this->startDate = now()->startOfWeek()->format('Y-m-d');
        $this->endDate = now()->endOfWeek()->format('Y-m-d');
        $this->generateReport();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\DatePicker::make('startDate')
                            ->label('Start Date')
                            ->default(now()->startOfWeek())
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn ($state) => $this->generateReport()),

                        Forms\Components\DatePicker::make('endDate')
                            ->label('End Date')
                            ->default(now()->endOfWeek())
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn ($state) => $this->generateReport()),
                    ]),
            ]);
    }

    public function generateReport(): void
    {
        if (! $this->startDate || ! $this->endDate) {
            return;
        }

        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

        // Ensure end date is not before start date
        if ($endDate->lt($startDate)) {
            $this->endDate = $startDate->format('Y-m-d');
            $endDate = $startDate;
        }

        // Debug log
        \Log::info('Generating date range report from: '.$startDate->format('Y-m-d').' to '.$endDate->format('Y-m-d'));

        // Get all time entries for the date range with eligible job positions
        $timeEntries = TimeEntry::whereBetween('in_date', [$startDate, $endDate])
            ->whereNotNull('job_title')
            ->get();

        \Log::info('Found time entries: '.$timeEntries->count());

        $this->tipsData = [];
        $this->dailyBreakdown = [];
        $totalPoints = 0;
        $totalTipsAmount = 0;

        // Group time entries by employee, job title, and date
        $groupedEntries = [];

        foreach ($timeEntries as $entry) {
            // Check if job position applies for tips
            $jobPosition = JobPosition::where('name', $entry->job_title)->first();

            if (! $jobPosition || ! $jobPosition->applies_for_tips) {
                continue;
            }

            $entryDate = Carbon::parse($entry->in_date)->format('Y-m-d');
            $key = $entry->employee_name.'|'.$entry->job_title.'|'.$entryDate;

            if (! isset($groupedEntries[$key])) {
                $groupedEntries[$key] = [
                    'employee_name' => $entry->employee_name,
                    'job_title' => $entry->job_title,
                    'date' => $entryDate,
                    'payable_hours' => 0,
                    'job_position' => $jobPosition,
                ];
            }

            $groupedEntries[$key]['payable_hours'] += $entry->payable_hours ?? 0;
        }

        // Process grouped entries and aggregate by employee across all dates
        $employeeAggregation = [];

        foreach ($groupedEntries as $groupedEntry) {
            $hoursWorked = $groupedEntry['payable_hours'];
            $jobPosition = $groupedEntry['job_position'];
            $date = $groupedEntry['date'];

            // Determine if qualifies for full points (5+ hours)
            $qualifiesForFullPoints = $hoursWorked >= 5.0;

            // Calculate points using rule of 3
            $jobPositionPoints = $jobPosition->points;
            $calculatedPoints = $qualifiesForFullPoints
                ? $jobPositionPoints
                : ($hoursWorked / 5.0) * $jobPositionPoints;

            $totalPoints += $calculatedPoints;

            // Add to daily breakdown
            if (! isset($this->dailyBreakdown[$date])) {
                $this->dailyBreakdown[$date] = [
                    'date' => $date,
                    'total_employees' => 0,
                    'total_points' => 0,
                    'total_tips' => 0,
                    'employees' => [],
                ];
            }

            $this->dailyBreakdown[$date]['total_points'] += $calculatedPoints;
            $this->dailyBreakdown[$date]['employees'][] = [
                'employee_name' => $groupedEntry['employee_name'],
                'job_title' => $groupedEntry['job_title'],
                'hours_worked' => $hoursWorked,
                'calculated_points' => $calculatedPoints,
            ];

            // Aggregate by employee across all dates
            $empKey = $groupedEntry['employee_name'].'|'.$groupedEntry['job_title'];

            if (! isset($employeeAggregation[$empKey])) {
                $employeeAggregation[$empKey] = [
                    'employee_name' => $groupedEntry['employee_name'],
                    'job_title' => $groupedEntry['job_title'],
                    'total_hours' => 0,
                    'total_points' => 0,
                    'days_worked' => 0,
                    'job_position_points' => $jobPositionPoints,
                ];
            }

            $employeeAggregation[$empKey]['total_hours'] += $hoursWorked;
            $employeeAggregation[$empKey]['total_points'] += $calculatedPoints;
            $employeeAggregation[$empKey]['days_worked']++;
        }

        // Get daily tips amounts for the date range
        $dailyTips = DailyTip::whereBetween('date', [$startDate, $endDate])->get();

        foreach ($dailyTips as $dailyTip) {
            $date = $dailyTip->date->format('Y-m-d');
            $totalTipsAmount += $dailyTip->amount;

            if (isset($this->dailyBreakdown[$date])) {
                $this->dailyBreakdown[$date]['total_tips'] = $dailyTip->amount;
            }
        }

        // Calculate tip per point
        $tipPerPoint = $totalPoints > 0 ? $totalTipsAmount / $totalPoints : 0;

        // Convert employee aggregation to final tips data
        foreach ($employeeAggregation as $empData) {
            $avgHoursPerDay = $empData['days_worked'] > 0 ? $empData['total_hours'] / $empData['days_worked'] : 0;

            $this->tipsData[] = [
                'employee_name' => $empData['employee_name'],
                'job_title' => $empData['job_title'],
                'total_hours' => $empData['total_hours'],
                'avg_hours_per_day' => round($avgHoursPerDay, 2),
                'days_worked' => $empData['days_worked'],
                'job_position_points' => $empData['job_position_points'],
                'total_calculated_points' => round($empData['total_points'], 2),
                'tip_amount' => round($empData['total_points'] * $tipPerPoint, 2),
            ];
        }

        // Update daily breakdown counts
        foreach ($this->dailyBreakdown as &$day) {
            $day['total_employees'] = count($day['employees']);
        }

        // Summary data
        $this->summary = [
            'date_range' => $startDate->format('M j, Y').' - '.$endDate->format('M j, Y'),
            'total_days' => $startDate->diffInDays($endDate) + 1,
            'total_employees' => count($this->tipsData),
            'total_points' => round($totalPoints, 2),
            'total_tips_amount' => $totalTipsAmount,
            'tip_per_point' => round($tipPerPoint, 2),
            'avg_tips_per_day' => count($dailyTips) > 0 ? round($totalTipsAmount / count($dailyTips), 2) : 0,
        ];

        // Sort by total calculated points descending
        usort($this->tipsData, fn ($a, $b) => $b['total_calculated_points'] <=> $a['total_calculated_points']);

        // Sort daily breakdown by date
        ksort($this->dailyBreakdown);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Refresh Report')
                ->icon('heroicon-o-arrow-path')
                ->action('generateReport')
                ->color('primary'),

            Action::make('export')
                ->label('Export Report')
                ->icon('heroicon-o-document-arrow-down')
                ->action('exportReport')
                ->color('success'),
        ];
    }

    public function exportReport(): void
    {
        Notification::make()
            ->title('Export functionality')
            ->body('Date range export feature will be implemented in the next phase.')
            ->info()
            ->send();
    }
}
