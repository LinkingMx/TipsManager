<?php

namespace App\Filament\Imports;

use App\Models\TimeEntry;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\Checkbox;

class TimeEntryImporter extends Importer
{
    protected static ?string $model = TimeEntry::class;

    public static function getColumns(): array
    {
        return [
            // Location Information
            ImportColumn::make('location')
                ->label('Location')
                ->example('Main Store')
                ->guess(['store', 'branch', 'site'])
                ->rules(['nullable', 'string', 'max:255']),

            ImportColumn::make('location_code')
                ->label('Location Code')
                ->example('MS001')
                ->guess(['store_code', 'branch_code'])
                ->rules(['nullable', 'string', 'max:50']),

            ImportColumn::make('external_id')
                ->label('External ID')
                ->example('EXT123456')
                ->guess(['id', 'record_id'])
                ->rules(['nullable', 'string', 'max:100']),

            // Employee Information
            ImportColumn::make('employee_id')
                ->label('Employee ID')
                ->example('1200000004664290000')
                ->guess(['employee_id', 'employee id', 'emp_id', 'staff_id', 'employee_number'])
                ->castStateUsing(function ($state): ?string {
                    if (blank($state)) {
                        return null;
                    }

                    // Convert numeric employee IDs to string to handle large numbers
                    return (string) $state;
                })
                ->rules(['nullable', 'string', 'max:50']),

            ImportColumn::make('employee_name')
                ->requiredMapping()
                ->label('Employee Name')
                ->example('John Doe')
                ->guess(['employee', 'name', 'full_name', 'staff_name'])
                ->rules(['required', 'string', 'max:255']),

            // Job Information
            ImportColumn::make('job_title')
                ->requiredMapping()
                ->label('Job Title')
                ->example('Server')
                ->guess(['job_title', 'job title', 'position', 'role', 'job'])
                ->rules(['required', 'string', 'max:255']),

            // Time Information
            ImportColumn::make('in_date')
                ->requiredMapping()
                ->label('Clock In Date/Time')
                ->example('2025-06-22 09:00:00')
                ->guess(['in_date', 'in date', 'start_time', 'clock_in', 'begin_time'])
                ->castStateUsing(function (string $state): ?\DateTime {
                    if (blank($state)) {
                        return null;
                    }
                    try {
                        return new \DateTime($state);
                    } catch (\Exception $e) {
                        return null;
                    }
                })
                ->rules(['required', 'date']),

            ImportColumn::make('out_date')
                ->label('Clock Out Date/Time')
                ->example('2025-06-22 17:00:00')
                ->guess(['out_date', 'out date', 'end_time', 'clock_out', 'finish_time'])
                ->castStateUsing(function (string $state): ?\DateTime {
                    if (blank($state)) {
                        return null;
                    }
                    try {
                        return new \DateTime($state);
                    } catch (\Exception $e) {
                        return null;
                    }
                })
                ->rules(['nullable', 'date']),

            // Hours Information
            ImportColumn::make('total_hours')
                ->label('Total Hours')
                ->numeric(decimalPlaces: 2)
                ->example('8.00')
                ->guess(['total_hours', 'total hours', 'hours', 'total_time'])
                ->rules(['nullable', 'numeric', 'min:0', 'max:24']),

            ImportColumn::make('payable_hours')
                ->label('Payable Hours')
                ->numeric(decimalPlaces: 2)
                ->example('7.50')
                ->guess(['payable_hours', 'payable hours', 'billable_hours', 'paid_hours'])
                ->rules(['nullable', 'numeric', 'min:0', 'max:24']),

            ImportColumn::make('regular_hours')
                ->label('Regular Hours')
                ->numeric(decimalPlaces: 2)
                ->example('8.00')
                ->guess(['regular_hours', 'regular hours', 'standard_hours'])
                ->rules(['nullable', 'numeric', 'min:0', 'max:24']),

            ImportColumn::make('overtime_hours')
                ->label('Overtime Hours')
                ->numeric(decimalPlaces: 2)
                ->example('2.00')
                ->guess(['overtime_hours', 'overtime hours', 'ot_hours', 'extra_hours'])
                ->rules(['nullable', 'numeric', 'min:0', 'max:12']),

            ImportColumn::make('unpaid_break_time')
                ->label('Unpaid Break Time')
                ->numeric(decimalPlaces: 2)
                ->example('0.50')
                ->guess(['unpaid_break_time', 'unpaid break time', 'break_time', 'unpaid_break'])
                ->rules(['nullable', 'numeric', 'min:0', 'max:4']),

            ImportColumn::make('paid_break_time')
                ->label('Paid Break Time')
                ->numeric(decimalPlaces: 2)
                ->example('0.25')
                ->guess(['paid_break_time', 'paid break time', 'paid_break'])
                ->guess(['paid_break'])
                ->rules(['nullable', 'numeric', 'min:0', 'max:4']),

            // Tips Information
            ImportColumn::make('cash_tips_declared')
                ->label('Cash Tips Declared')
                ->numeric(decimalPlaces: 2)
                ->example('45.50')
                ->guess(['cash_tips', 'tips_cash'])
                ->rules(['nullable', 'numeric', 'min:0']),

            ImportColumn::make('non_cash_tips')
                ->label('Non-Cash Tips')
                ->numeric(decimalPlaces: 2)
                ->example('12.30')
                ->guess(['card_tips', 'electronic_tips'])
                ->rules(['nullable', 'numeric', 'min:0']),

            ImportColumn::make('total_tips')
                ->label('Total Tips')
                ->numeric(decimalPlaces: 2)
                ->example('57.80')
                ->guess(['tips_total', 'all_tips'])
                ->rules(['nullable', 'numeric', 'min:0']),

            ImportColumn::make('tips_withheld')
                ->label('Tips Withheld')
                ->numeric(decimalPlaces: 2)
                ->example('5.78')
                ->guess(['withheld_tips'])
                ->rules(['nullable', 'numeric', 'min:0']),

            // Pay Information
            ImportColumn::make('wage')
                ->label('Hourly Wage')
                ->numeric(decimalPlaces: 2)
                ->example('15.50')
                ->guess(['hourly_rate', 'pay_rate'])
                ->rules(['nullable', 'numeric', 'min:0']),

            ImportColumn::make('regular_pay')
                ->label('Regular Pay')
                ->numeric(decimalPlaces: 2)
                ->example('124.00')
                ->guess(['base_pay', 'standard_pay'])
                ->rules(['nullable', 'numeric', 'min:0']),

            ImportColumn::make('overtime_pay')
                ->label('Overtime Pay')
                ->numeric(decimalPlaces: 2)
                ->example('46.50')
                ->guess(['ot_pay', 'extra_pay'])
                ->rules(['nullable', 'numeric', 'min:0']),

            ImportColumn::make('total_pay')
                ->label('Total Pay')
                ->numeric(decimalPlaces: 2)
                ->example('170.50')
                ->guess(['gross_pay', 'total_wages'])
                ->rules(['nullable', 'numeric', 'min:0']),

            // Additional optional fields that might be in CSV
            ImportColumn::make('auto_clock_out')
                ->label('Auto Clock Out')
                ->boolean()
                ->example('false')
                ->guess(['auto_out', 'automatic_checkout'])
                ->rules(['boolean']),
        ];
    }

    public function resolveRecord(): ?TimeEntry
    {
        // Check if updateExisting option is enabled
        if ($this->options['updateExisting'] ?? false) {
            // Try to find existing record by external_id first, then by employee_name and in_date
            if (! empty($this->data['external_id'])) {
                return TimeEntry::firstOrNew([
                    'external_id' => $this->data['external_id'],
                ]);
            }

            // Fallback to employee_name and in_date combination
            return TimeEntry::firstOrNew([
                'employee_name' => $this->data['employee_name'],
                'in_date' => $this->data['in_date'],
            ]);
        }

        return new TimeEntry;
    }

    public static function getOptionsFormComponents(): array
    {
        return [
            Checkbox::make('updateExisting')
                ->label('Update existing time entries')
                ->helperText('If enabled, existing time entries with the same external ID or employee name + clock-in time will be updated instead of creating duplicates.'),
        ];
    }

    public function getValidationMessages(): array
    {
        return [
            'employee_name.required' => 'The employee name field is required.',
            'job_title.required' => 'The job title field is required.',
            'in_date.required' => 'The clock-in date/time field is required.',
            'in_date.date' => 'The clock-in date/time must be a valid date.',
            'out_date.date' => 'The clock-out date/time must be a valid date.',
            'total_hours.numeric' => 'Total hours must be a number.',
            'total_hours.max' => 'Total hours cannot exceed 24 hours.',
            'payable_hours.numeric' => 'Payable hours must be a number.',
            'wage.numeric' => 'Wage must be a number.',
            'tips_total.numeric' => 'Total tips must be a number.',
        ];
    }

    // Process imports immediately (not queued)
    public function getJobQueue(): ?string
    {
        return null; // Returns null to process synchronously
    }

    public function getJobConnection(): ?string
    {
        return 'sync'; // Force synchronous processing
    }

    protected function beforeSave(): void
    {
        // Log import activity or perform any pre-save logic if needed
    }

    protected function afterSave(): void
    {
        // Log successful import or perform any post-save logic if needed
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your time entry import has completed and '.number_format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
