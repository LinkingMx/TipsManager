<?php

namespace App\Filament\Pages;

use App\Exports\DateRangeTipsPoolExport;
use App\Exports\PayrollExport;
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

        $this->tipsData = [];
        $this->dailyBreakdown = [];
        $totalPoints = 0;
        $totalTipsAmount = 0;

        // Process each day individually (like Daily Tips Pool) then aggregate
        $current = $startDate->copy();
        $allEmployeeData = []; // To accumulate employee data across days

        while ($current->lte($endDate)) {
            $dateString = $current->format('Y-m-d');

            // Get time entries for this specific date
            $timeEntries = TimeEntry::whereDate('in_date', $current)
                ->whereNotNull('job_title')
                ->get();

            // Group time entries by employee and job title for this date
            $groupedEntries = [];

            foreach ($timeEntries as $entry) {
                // Check if job position applies for tips
                $jobPosition = JobPosition::where('name', $entry->job_title)->first();

                if (! $jobPosition || ! $jobPosition->applies_for_tips) {
                    continue;
                }

                $key = $entry->employee_name.'|'.$entry->job_title;

                if (! isset($groupedEntries[$key])) {
                    $groupedEntries[$key] = [
                        'employee_name' => $entry->employee_name,
                        'job_title' => $entry->job_title,
                        'payable_hours' => 0,
                        'job_position' => $jobPosition,
                    ];
                }

                $groupedEntries[$key]['payable_hours'] += $entry->payable_hours ?? 0;
            }

            // Calculate points for this day (same logic as Daily Tips Pool)
            $dayPoints = 0;
            $dayEmployees = [];

            foreach ($groupedEntries as $groupedEntry) {
                $hoursWorked = $groupedEntry['payable_hours'];
                $jobPosition = $groupedEntry['job_position'];

                // Determine if qualifies for full points (5+ hours)
                $qualifiesForFullPoints = $hoursWorked >= 5.0;

                // Calculate points using rule of 3
                $jobPositionPoints = $jobPosition->points;
                $calculatedPoints = $qualifiesForFullPoints
                    ? $jobPositionPoints
                    : ($hoursWorked / 5.0) * $jobPositionPoints;

                $dayPoints += $calculatedPoints;

                $dayEmployees[] = [
                    'employee_name' => $groupedEntry['employee_name'],
                    'job_title' => $groupedEntry['job_title'],
                    'hours_worked' => $hoursWorked,
                    'calculated_points' => $calculatedPoints,
                ];
            }

            // Get daily tips amount for this date
            $dailyTip = DailyTip::whereDate('date', $current)->first();
            $dayTipsAmount = $dailyTip ? $dailyTip->amount : 0;
            $totalTipsAmount += $dayTipsAmount;

            // Calculate tip per point for THIS DAY (same as Daily Tips Pool)
            $dayTipPerPoint = $dayPoints > 0 ? $dayTipsAmount / $dayPoints : 0;

            // Distribute tips to employees for this day and accumulate
            foreach ($dayEmployees as $empData) {
                $empKey = $empData['employee_name'].'|'.$empData['job_title'];
                $employeeDailyTips = $empData['calculated_points'] * $dayTipPerPoint;

                if (! isset($allEmployeeData[$empKey])) {
                    $allEmployeeData[$empKey] = [
                        'employee_name' => $empData['employee_name'],
                        'job_title' => $empData['job_title'],
                        'total_hours' => 0,
                        'total_points' => 0,
                        'total_tips' => 0,
                        'days_worked' => 0,
                        'job_position_points' => 0,
                    ];
                }

                $allEmployeeData[$empKey]['total_hours'] += $empData['hours_worked'];
                $allEmployeeData[$empKey]['total_points'] += $empData['calculated_points'];
                $allEmployeeData[$empKey]['total_tips'] += $employeeDailyTips;
                $allEmployeeData[$empKey]['days_worked']++;

                // Get job position points for display
                $jobPosition = JobPosition::where('name', $empData['job_title'])->first();
                if ($jobPosition) {
                    $allEmployeeData[$empKey]['job_position_points'] = $jobPosition->points;
                }
            }

            $totalPoints += $dayPoints;

            // Add to daily breakdown
            $this->dailyBreakdown[$dateString] = [
                'date' => $dateString,
                'total_employees' => count($dayEmployees),
                'total_points' => round($dayPoints, 2),
                'total_tips' => $dayTipsAmount,
                'employees' => $dayEmployees,
            ];

            $current->addDay();
        }

        // Convert employee aggregation to final tips data
        foreach ($allEmployeeData as $empData) {
            $avgHoursPerDay = $empData['days_worked'] > 0 ? $empData['total_hours'] / $empData['days_worked'] : 0;

            $this->tipsData[] = [
                'employee_name' => $empData['employee_name'],
                'job_title' => $empData['job_title'],
                'total_hours' => $empData['total_hours'],
                'avg_hours_per_day' => round($avgHoursPerDay, 2),
                'days_worked' => $empData['days_worked'],
                'job_position_points' => $empData['job_position_points'],
                'total_calculated_points' => round($empData['total_points'], 2),
                'tip_amount' => round($empData['total_tips'], 2),
            ];
        }

        // Summary data
        $this->summary = [
            'date_range' => $startDate->format('M j, Y').' - '.$endDate->format('M j, Y'),
            'total_days' => $startDate->diffInDays($endDate) + 1,
            'total_employees' => count($this->tipsData),
            'total_points' => round($totalPoints, 2),
            'total_tips_amount' => $totalTipsAmount,
            'tip_per_point' => $totalPoints > 0 ? round($totalTipsAmount / $totalPoints, 2) : 0,
            'avg_tips_per_day' => count($this->dailyBreakdown) > 0 ? round($totalTipsAmount / count($this->dailyBreakdown), 2) : 0,
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
                ->action(function () {
                    return $this->exportReport();
                })
                ->color('success'),

            Action::make('exportPayroll')
                ->label('Export Payroll')
                ->icon('heroicon-o-banknotes')
                ->action(function () {
                    return $this->exportPayroll();
                })
                ->color('warning'),
        ];
    }

    public function exportReport()
    {
        try {
            // Ensure we have data to export
            if (empty($this->summary) || empty($this->tipsData)) {
                $this->generateReport();
            }

            // Generate filename with date range
            $startDate = Carbon::parse($this->startDate)->format('Y-m-d');
            $endDate = Carbon::parse($this->endDate)->format('Y-m-d');
            $filename = "date-range-tips-pool_{$startDate}_to_{$endDate}.xlsx";

            // Show success notification
            Notification::make()
                ->title('Exportando...')
                ->body('Preparando archivo Excel para descargar.')
                ->success()
                ->send();

            // Use the Exportable trait method to download directly
            return (new DateRangeTipsPoolExport($this->summary, $this->tipsData, $this->dailyBreakdown))
                ->download($filename);

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error de Exportación')
                ->body('Falló la exportación: '.$e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function exportPayroll()
    {
        try {
            // Ensure we have data to export
            if (empty($this->summary) || empty($this->tipsData)) {
                $this->generateReport();
            }

            // Check if there's data to export
            if (empty($this->tipsData)) {
                Notification::make()
                    ->title('No Data to Export')
                    ->body('No tips data found for the selected date range.')
                    ->warning()
                    ->send();

                return;
            }

            // Generate filename with date range
            $startDate = Carbon::parse($this->startDate)->format('Y-m-d');
            $endDate = Carbon::parse($this->endDate)->format('Y-m-d');
            $filename = "payroll_export_{$startDate}_to_{$endDate}.xlsx";

            // Show success notification
            Notification::make()
                ->title('Exportando Payroll...')
                ->body('Preparando archivo Excel de nómina para descargar.')
                ->success()
                ->send();

            // Create and download the Payroll Excel file
            return (new PayrollExport($this->summary, $this->tipsData))
                ->download($filename);

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error de Exportación Payroll')
                ->body('Falló la exportación de nómina: '.$e->getMessage())
                ->danger()
                ->send();
        }
    }
}
