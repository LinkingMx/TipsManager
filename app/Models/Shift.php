<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'start_hour',
        'end_hour',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_hour' => 'datetime:H:i',
            'end_hour' => 'datetime:H:i',
        ];
    }

    /**
     * Get the duration of the shift in hours
     */
    public function getDurationAttribute(): float
    {
        if ($this->start_hour && $this->end_hour) {
            $start = \Carbon\Carbon::parse($this->start_hour);
            $end = \Carbon\Carbon::parse($this->end_hour);

            // Handle overnight shifts
            if ($end->lessThan($start)) {
                $end->addDay();
            }

            return $start->diffInHours($end, true);
        }

        return 0;
    }

    /**
     * Check if this is an overnight shift
     */
    public function isOvernightShift(): bool
    {
        if ($this->start_hour && $this->end_hour) {
            $start = \Carbon\Carbon::parse($this->start_hour);
            $end = \Carbon\Carbon::parse($this->end_hour);

            return $end->lessThan($start);
        }

        return false;
    }

    /**
     * Get formatted time range
     */
    public function getTimeRangeAttribute(): string
    {
        if ($this->start_hour && $this->end_hour) {
            $start = \Carbon\Carbon::parse($this->start_hour)->format('g:i A');
            $end = \Carbon\Carbon::parse($this->end_hour)->format('g:i A');

            return "{$start} - {$end}";
        }

        return '';
    }
}
