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

        // Get all time entries for the selected date with eligible job positions
        $timeEntries = TimeEntry::whereDate('in_date', $date)
            ->whereNotNull('job_title')
            ->get();

        \Log::info('Found time entries: '.$timeEntries->count());

        $this->tipsData = [];
        $totalPoints = 0;

        // Group time entries by employee and job title
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

        // Process grouped entries
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

            $this->tipsData[] = [
                'employee_name' => $groupedEntry['employee_name'],
                'job_title' => $groupedEntry['job_title'],
                'hours_worked' => $hoursWorked,
                'job_position_points' => $jobPositionPoints,
                'calculated_points' => round($calculatedPoints, 2),
                'qualifies_for_full_points' => $qualifiesForFullPoints,
                'percentage' => $jobPositionPoints > 0 ? round(($calculatedPoints / $jobPositionPoints) * 100, 1) : 0,
            ];
        }

        // Get daily tips amount for the selected date
        $dailyTip = DailyTip::whereDate('date', $date)->first();
        $totalTipsAmount = $dailyTip ? $dailyTip->amount : 0;

        // Calculate tip per point
        $tipPerPoint = $totalPoints > 0 ? $totalTipsAmount / $totalPoints : 0;

        // Calculate individual tip amounts
        foreach ($this->tipsData as &$data) {
            $data['tip_amount'] = round($data['calculated_points'] * $tipPerPoint, 2);
        }

        // Summary data
        $this->summary = [
            'total_employees' => count($this->tipsData),
            'total_points' => round($totalPoints, 2),
            'total_tips_amount' => $totalTipsAmount,
            'tip_per_point' => round($tipPerPoint, 2),
            'employees_full_points' => count(array_filter($this->tipsData, fn ($d) => $d['qualifies_for_full_points'])),
            'employees_partial_points' => count(array_filter($this->tipsData, fn ($d) => ! $d['qualifies_for_full_points'])),
        ];

        // Sort by calculated points descending
        usort($this->tipsData, fn ($a, $b) => $b['calculated_points'] <=> $a['calculated_points']);
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
            ->body('Export feature will be implemented in the next phase.')
            ->info()
            ->send();
    }
}
