<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('daily_tips', function (Blueprint $table) {
            // Drop the existing unique constraint on date
            $table->dropUnique(['date']);

            // Add a composite unique constraint for date and shift_period
            $table->unique(['date', 'shift_period'], 'daily_tips_date_shift_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_tips', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('daily_tips_date_shift_unique');

            // Restore the original unique constraint on date
            $table->unique('date');
        });
    }
};
