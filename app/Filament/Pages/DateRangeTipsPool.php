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
        $totalAmTips = 0;
        $totalPmTips = 0;

        // Process each day individually using the same logic as Daily Tips Pool
        $current = $startDate->copy();
        $allEmployeeData = []; // To accumulate employee data across days

        while ($current->lte($endDate)) {
            $dateString = $current->format('Y-m-d');

            // Get BOTH AM and PM tips for this date (same as TipsPool)
            $amTips = DailyTip::whereDate('date', $current)
                ->where('shift_period', 'AM')
                ->first();

            $pmTips = DailyTip::whereDate('date', $current)
                ->where('shift_period', 'PM')
                ->first();

            // Get time entries for this specific date
            $timeEntries = TimeEntry::whereDate('in_date', $current)
                ->whereNotNull('job_title')
                ->get();

            // Separate employees by shift based on in_date time (same as TipsPool)
            $amEmployees = [];
            $pmEmployees = [];

            foreach ($timeEntries as $entry) {
                $inTime = Carbon::parse($entry->in_date);
                $cutoffTime = $current->copy()->setTime(14, 0, 0); // 2:00 PM

                if ($inTime->lt($cutoffTime)) {
                    $amEmployees[] = $entry;
                } else {
                    $pmEmployees[] = $entry;
                }
            }

            // Process AM shift for this day
            $amResults = $this->processShiftForDate($amEmployees, $amTips, 'AM');

            // Process PM shift for this day
            $pmResults = $this->processShiftForDate($pmEmployees, $pmTips, 'PM');

            // Combine results for this day
            $dayResults = array_merge($amResults, $pmResults);

            // Accumulate employee data across the date range
            foreach ($dayResults as $empData) {
                $empKey = $empData['employee_name'].'|'.$empData['job_title'].'|'.$empData['shift'];

                if (! isset($allEmployeeData[$empKey])) {
                    $allEmployeeData[$empKey] = [
                        'employee_name' => $empData['employee_name'],
                        'job_title' => $empData['job_title'],
                        'shift' => $empData['shift'],
                        'total_hours' => 0,
                        'total_points' => 0,
                        'total_tips' => 0,
                        'days_worked' => 0,
                        'job_position_points' => $empData['job_position_points'],
                    ];
                }

                $allEmployeeData[$empKey]['total_hours'] += $empData['hours_worked'];
                $allEmployeeData[$empKey]['total_points'] += $empData['calculated_points'];
                $allEmployeeData[$empKey]['total_tips'] += $empData['tip_amount'];
                $allEmployeeData[$empKey]['days_worked']++;
            }

            // Track daily totals
            $dayAmTips = $amTips ? $amTips->amount : 0;
            $dayPmTips = $pmTips ? $pmTips->amount : 0;
            $dayTotalTips = $dayAmTips + $dayPmTips;
            $dayTotalPoints = array_sum(array_column($dayResults, 'calculated_points'));

            $totalAmTips += $dayAmTips;
            $totalPmTips += $dayPmTips;
            $totalTipsAmount += $dayTotalTips;
            $totalPoints += $dayTotalPoints;

            // Add to daily breakdown
            $this->dailyBreakdown[$dateString] = [
                'date' => $dateString,
                'am_employees' => count($amResults),
                'pm_employees' => count($pmResults),
                'total_employees' => count($dayResults),
                'am_tips' => $dayAmTips,
                'pm_tips' => $dayPmTips,
                'total_tips' => $dayTotalTips,
                'total_points' => round($dayTotalPoints, 2),
                'employees' => $dayResults,
            ];

            $current->addDay();
        }

        // Convert employee aggregation to final tips data
        foreach ($allEmployeeData as $empData) {
            $avgHoursPerDay = $empData['days_worked'] > 0 ? $empData['total_hours'] / $empData['days_worked'] : 0;

            $this->tipsData[] = [
                'employee_name' => $empData['employee_name'],
                'job_title' => $empData['job_title'],
                'shift' => $empData['shift'],
                'total_hours' => $empData['total_hours'],
                'avg_hours_per_day' => round($avgHoursPerDay, 2),
                'days_worked' => $empData['days_worked'],
                'job_position_points' => $empData['job_position_points'],
                'total_calculated_points' => round($empData['total_points'], 2),
                'tip_amount' => round($empData['total_tips'], 2),
            ];
        }

        // Summary data with AM/PM breakdown
        $this->summary = [
            'date_range' => $startDate->format('M j, Y').' - '.$endDate->format('M j, Y'),
            'total_days' => $startDate->diffInDays($endDate) + 1,
            'total_employees' => count($this->tipsData),
            'am_employees' => count(array_filter($this->tipsData, fn ($emp) => $emp['shift'] === 'AM')),
            'pm_employees' => count(array_filter($this->tipsData, fn ($emp) => $emp['shift'] === 'PM')),
            'total_points' => round($totalPoints, 2),
            'am_tips_amount' => $totalAmTips,
            'pm_tips_amount' => $totalPmTips,
            'total_tips_amount' => $totalTipsAmount,
            'tip_per_point' => $totalPoints > 0 ? round($totalTipsAmount / $totalPoints, 2) : 0,
            'avg_tips_per_day' => count($this->dailyBreakdown) > 0 ? round($totalTipsAmount / count($this->dailyBreakdown), 2) : 0,
        ];

        // Sort by total calculated points descending
        usort($this->tipsData, fn ($a, $b) => $b['total_calculated_points'] <=> $a['total_calculated_points']);

        // Sort daily breakdown by date
        ksort($this->dailyBreakdown);
    }

    private function processShiftForDate($employees, $dailyTip, $shift)
    {
        if (! $dailyTip || ! $dailyTip->amount || empty($employees)) {
            return [];
        }

        // Group employees by employee name and job title for this shift
        $groupedEntries = [];

        foreach ($employees as $entry) {
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
                    'shift' => $shift,
                ];
            }

            $groupedEntries[$key]['payable_hours'] += $entry->payable_hours ?? 0;
        }

        // Calculate tips for this shift (same logic as TipsPool)
        $shiftResults = [];
        $totalPoints = 0;

        // Process grouped entries for this shift
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

            $totalPoints += $calculatedPoints;

            $shiftResults[] = [
                'employee_name' => $groupedEntry['employee_name'],
                'job_title' => $groupedEntry['job_title'],
                'shift' => $shift,
                'hours_worked' => $hoursWorked,
                'job_position_points' => $jobPositionPoints,
                'calculated_points' => round($calculatedPoints, 2),
                'qualifies_for_full_points' => $qualifiesForFullPoints,
                'percentage' => $jobPositionPoints > 0 ? round(($calculatedPoints / $jobPositionPoints) * 100, 1) : 0,
            ];
        }

        // Calculate tip per point for this shift
        $tipPerPoint = $totalPoints > 0 ? $dailyTip->amount / $totalPoints : 0;

        // Assign tip amounts
        foreach ($shiftResults as &$employee) {
            $employee['tip_amount'] = round($employee['calculated_points'] * $tipPerPoint, 2);
        }

        return $shiftResults;
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
