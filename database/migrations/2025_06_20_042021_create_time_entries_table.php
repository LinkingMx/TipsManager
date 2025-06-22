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
        Schema::create('time_entries', function (Blueprint $table) {
            $table->id();

            // Location Information
            $table->string('location')->nullable();
            $table->string('location_code')->nullable();
            $table->string('external_id')->nullable();
            $table->uuid('guid')->nullable();

            // Employee Information
            $table->string('employee_id')->nullable();
            $table->uuid('employee_guid')->nullable();
            $table->string('employee_external_id')->nullable();
            $table->string('employee_name')->nullable();

            // Job Information
            $table->string('job_id')->nullable();
            $table->uuid('job_guid')->nullable();
            $table->string('job_code')->nullable();
            $table->string('job_title')->nullable();

            // Time Information
            $table->dateTime('in_date')->nullable();
            $table->dateTime('out_date')->nullable();
            $table->boolean('auto_clock_out')->default(false);

            // Hours Information
            $table->decimal('total_hours', 8, 2)->nullable();
            $table->decimal('unpaid_break_time', 8, 2)->nullable();
            $table->decimal('paid_break_time', 8, 2)->nullable();
            $table->decimal('payable_hours', 8, 2)->nullable();

            // Tips Information
            $table->decimal('cash_tips_declared', 8, 2)->nullable();
            $table->decimal('non_cash_tips', 8, 2)->nullable();
            $table->decimal('total_gratuity', 8, 2)->nullable();
            $table->decimal('total_tips', 8, 2)->nullable();
            $table->decimal('tips_withheld', 8, 2)->nullable();

            // Pay Information
            $table->decimal('wage', 8, 2)->nullable();
            $table->decimal('regular_hours', 8, 2)->nullable();
            $table->decimal('overtime_hours', 8, 2)->nullable();
            $table->decimal('regular_pay', 8, 2)->nullable();
            $table->decimal('overtime_pay', 8, 2)->nullable();
            $table->decimal('total_pay', 8, 2)->nullable();

            $table->timestamps();

            // Indexes for better performance
            $table->index(['employee_external_id', 'in_date']);
            $table->index(['job_title', 'in_date']);
            $table->index('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_entries');
    }
};
