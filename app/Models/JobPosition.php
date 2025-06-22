<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPosition extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'points',
        'applies_for_tips',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'points' => 'decimal:2',
            'applies_for_tips' => 'boolean',
        ];
    }

    /**
     * Check if this position is eligible for tips
     */
    public function isEligibleForTips(): bool
    {
        return $this->applies_for_tips;
    }

    /**
     * Scope to get only positions that apply for tips
     */
    public function scopeTipsEligible($query)
    {
        return $query->where('applies_for_tips', true);
    }

    /**
     * Scope to get only positions that don't apply for tips
     */
    public function scopeNotTipsEligible($query)
    {
        return $query->where('applies_for_tips', false);
    }
}
