<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyTip extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'amount',
        'shift_period',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Get formatted amount with currency symbol
     */
    public function getFormattedAmountAttribute(): string
    {
        return '$'.number_format($this->amount, 2);
    }

    /**
     * Get day of week for the date
     */
    public function getDayOfWeekAttribute(): string
    {
        return $this->date->format('l');
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by current month
     */
    public function scopeCurrentMonth($query)
    {
        return $query->whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year);
    }

    /**
     * Scope to filter by current week
     */
    public function scopeCurrentWeek($query)
    {
        return $query->whereBetween('date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek(),
        ]);
    }

    /**
     * Get total tips for a date range
     */
    public static function getTotalForPeriod($startDate, $endDate): float
    {
        return static::dateRange($startDate, $endDate)->sum('amount');
    }

    /**
     * Get average tips for a date range
     */
    public static function getAverageForPeriod($startDate, $endDate): float
    {
        return static::dateRange($startDate, $endDate)->avg('amount') ?? 0;
    }
}
