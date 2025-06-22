<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeEntry extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Location Information
        'location',
        'location_code',
        'external_id',
        'guid',

        // Employee Information
        'employee_id',
        'employee_guid',
        'employee_external_id',
        'employee_name',

        // Job Information
        'job_id',
        'job_guid',
        'job_code',
        'job_title',

        // Time Information
        'in_date',
        'out_date',
        'auto_clock_out',

        // Hours Information
        'total_hours',
        'unpaid_break_time',
        'paid_break_time',
        'payable_hours',

        // Tips Information
        'cash_tips_declared',
        'non_cash_tips',
        'total_gratuity',
        'total_tips',
        'tips_withheld',

        // Pay Information
        'wage',
        'regular_hours',
        'overtime_hours',
        'regular_pay',
        'overtime_pay',
        'total_pay',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'in_date' => 'datetime',
            'out_date' => 'datetime',
            'auto_clock_out' => 'boolean',
            'guid' => 'string',
            'employee_guid' => 'string',
            'job_guid' => 'string',

            // Decimal casts for monetary values
            'total_hours' => 'decimal:2',
            'unpaid_break_time' => 'decimal:2',
            'paid_break_time' => 'decimal:2',
            'payable_hours' => 'decimal:2',
            'cash_tips_declared' => 'decimal:2',
            'non_cash_tips' => 'decimal:2',
            'total_gratuity' => 'decimal:2',
            'total_tips' => 'decimal:2',
            'tips_withheld' => 'decimal:2',
            'wage' => 'decimal:2',
            'regular_hours' => 'decimal:2',
            'overtime_hours' => 'decimal:2',
            'regular_pay' => 'decimal:2',
            'overtime_pay' => 'decimal:2',
            'total_pay' => 'decimal:2',
        ];
    }

    /**
     * Calculate worked hours based on in and out dates
     */
    public function getWorkedHoursAttribute(): float
    {
        if ($this->in_date && $this->out_date) {
            return $this->in_date->diffInHours($this->out_date, true);
        }

        return 0;
    }

    /**
     * Check if this is an overtime entry
     */
    public function isOvertime(): bool
    {
        return $this->overtime_hours > 0;
    }

    /**
     * Get total compensation including tips
     */
    public function getTotalCompensationAttribute(): float
    {
        return ($this->total_pay ?? 0) + ($this->total_tips ?? 0);
    }
}
