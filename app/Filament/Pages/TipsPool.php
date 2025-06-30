<?php

namespace App\Filament\Pages;

use App\Exports\DailyTipsPoolExport;
use App\Models\DailyTip;
use App\Models\JobPosition;
use App\Models\TimeEntry;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class TipsPool extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Tips Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Daily Tips Pool';

    protected static ?string $navigationLabel = 'Daily Tips Pool';

    protected static string $view = 'filament.pages.tips-pool';

    public ?string $selectedDate = null;

    public array $tipsData = [];

    public array $summary = [];

    public function mount(): void
    {
        $this->selectedDate = now()->format('Y-m-d');
        $this->generateReport();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('selectedDate')
                    ->label('Select Date for Tips Pool Report')
                    ->default(now())
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn ($state) => $this->generateReport()),
            ]);
    }

    public function generateReport(): void
    {
        if (! $this->selectedDate) {
            return;
        }

        $date = Carbon::parse($this->selectedDate);

        // Debug log
        \Log::info('Generating report for date: '.$date->format('Y-m-d'));

        // Get BOTH AM and PM tips for the selected date
        $amTips = DailyTip::whereDate('date', $date)
            ->where('shift_period', 'AM')
            ->first();

        $pmTips = DailyTip::whereDate('date', $date)
            ->where('shift_period', 'PM')
            ->first();

        // Get all time entries for the selected date with eligible job positions
        $timeEntries = TimeEntry::whereDate('in_date', $date)
            ->whereNotNull('job_title')
            ->get();

        \Log::info('Found time entries: '.$timeEntries->count());

        // Check if there are employees with tip-eligible positions
        $eligibleEmployees = 0;
        foreach ($timeEntries as $entry) {
            $jobPosition = JobPosition::where('name', $entry->job_title)->first();
            if ($jobPosition && $jobPosition->applies_for_tips) {
                $eligibleEmployees++;
            }
        }

        // Separate employees by shift based on in_date time
        $amEmployees = [];
        $pmEmployees = [];

        foreach ($timeEntries as $entry) {
            $inTime = Carbon::parse($entry->in_date);
            $cutoffTime = $date->copy()->setTime(14, 0, 0); // 2:00 PM

            if ($inTime->lt($cutoffTime)) {
                $amEmployees[] = $entry;
            } else {
                $pmEmployees[] = $entry;
            }
        }

        // Process AM shift
        $amResults = $this->processShift($amEmployees, $amTips, 'AM');

        // Process PM shift
        $pmResults = $this->processShift($pmEmployees, $pmTips, 'PM');

        // Combine results
        $this->tipsData = array_merge($amResults, $pmResults);

        // Update summary to show both shifts
        $this->summary = [
            'selected_date' => $date->format('Y-m-d'),
            'am_tips_amount' => $amTips ? $amTips->amount : 0,
            'pm_tips_amount' => $pmTips ? $pmTips->amount : 0,
            'total_tips_amount' => ($amTips ? $amTips->amount : 0) + ($pmTips ? $pmTips->amount : 0),
            'am_employees' => count($amResults),
            'pm_employees' => count($pmResults),
            'total_employees' => count($this->tipsData),
            'eligible_employees_count' => $eligibleEmployees,
            'am_total_points' => array_sum(array_column($amResults, 'calculated_points')),
            'pm_total_points' => array_sum(array_column($pmResults, 'calculated_points')),
            'total_points' => array_sum(array_column($this->tipsData, 'calculated_points')),
            'am_tip_per_point' => count($amResults) > 0 && array_sum(array_column($amResults, 'calculated_points')) > 0 ? (($amTips ? $amTips->amount : 0) / array_sum(array_column($amResults, 'calculated_points'))) : 0,
            'pm_tip_per_point' => count($pmResults) > 0 && array_sum(array_column($pmResults, 'calculated_points')) > 0 ? (($pmTips ? $pmTips->amount : 0) / array_sum(array_column($pmResults, 'calculated_points'))) : 0,
        ];

        // Sort by calculated points descending
        usort($this->tipsData, fn ($a, $b) => $b['calculated_points'] <=> $a['calculated_points']);
    }

    private function processShift($employees, $dailyTip, $shift)
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
                    'unpaid_break_time' => 0,
                    'job_position' => $jobPosition,
                    'in_date' => $entry->in_date,
                    'out_date' => $entry->out_date,
                    'shift' => $shift,
                ];
            } else {
                // Update dates if this entry has more recent times
                if (! $groupedEntries[$key]['in_date'] || $entry->in_date < $groupedEntries[$key]['in_date']) {
                    $groupedEntries[$key]['in_date'] = $entry->in_date;
                }
                if (! $groupedEntries[$key]['out_date'] || $entry->out_date > $groupedEntries[$key]['out_date']) {
                    $groupedEntries[$key]['out_date'] = $entry->out_date;
                }
            }

            $groupedEntries[$key]['payable_hours'] += $entry->payable_hours ?? 0;
            $groupedEntries[$key]['unpaid_break_time'] += $entry->unpaid_break_time ?? 0;
        }

        // Calculate tips for this shift
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
                'in_date' => $groupedEntry['in_date'],
                'out_date' => $groupedEntry['out_date'],
                'unpaid_break_time' => $groupedEntry['unpaid_break_time'],
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
        ];
    }

    public function exportReport()
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
                    ->body('No tips data found for the selected date.')
                    ->warning()
                    ->send();

                return;
            }

            // Generate filename with selected date
            $date = Carbon::parse($this->selectedDate)->format('Y-m-d');
            $filename = "daily-tips-pool_{$date}.xlsx";

            // Show success notification
            Notification::make()
                ->title('Exportando...')
                ->body('Preparando archivo Excel para descargar.')
                ->success()
                ->send();

            // Create and download the Excel file
            return (new DailyTipsPoolExport($this->summary, $this->tipsData, $this->selectedDate))
                ->download($filename);

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error de Exportación')
                ->body('Falló la exportación: '.$e->getMessage())
                ->danger()
                ->send();
        }
    }
}
