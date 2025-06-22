<x-filament-panels::page>
    <div class="space-y-10 p-4 sm:p-6">
        {{-- Date Range Filter Form --}}
        <div class="fi-section mb-4">
            <div
                class="fi-section-content p-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    Select Date Range for Tips Pool Report
                </h2>
                {{ $this->form }}
            </div>
        </div>

        {{-- Summary Cards Grid - 6 Columns for Date Range --}}
        @if (!empty($summary))
            <div class="fi-section">
                <div class="fi-section-content">
                    <div class="mb-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6 gap-6">
                        {{-- Date Range Card --}}
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="p-6">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0 p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                        <x-heroicon-o-calendar-days
                                            class="h-8 w-8 text-purple-600 dark:text-purple-400" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Date Range
                                        </p>
                                        <p class="text-base font-bold text-gray-900 dark:text-gray-100">
                                            {{ $summary['date_range'] }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                            {{ $summary['total_days'] }} day{{ $summary['total_days'] > 1 ? 's' : '' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Total Employees Card --}}
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="p-6">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                        <x-heroicon-o-users class="h-8 w-8 text-blue-600 dark:text-blue-400" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Total
                                            Employees</p>
                                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                                            {{ $summary['total_employees'] }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Unique employees</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Total Points Card --}}
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="p-6">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                        <x-heroicon-o-calculator class="h-8 w-8 text-green-600 dark:text-green-400" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Total
                                            Points</p>
                                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                                            {{ number_format($summary['total_points'], 2) }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Points earned</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Total Tips Card --}}
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="p-6">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                                        <x-heroicon-o-banknotes class="h-8 w-8 text-yellow-600 dark:text-yellow-400" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Total Tips
                                        </p>
                                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                                            ${{ number_format($summary['total_tips_amount'], 2) }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">To distribute</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Per Point Card --}}
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="p-6">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0 p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg">
                                        <x-heroicon-o-currency-dollar
                                            class="h-8 w-8 text-indigo-600 dark:text-indigo-400" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Per Point
                                        </p>
                                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                                            ${{ number_format($summary['tip_per_point'], 2) }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Rate per point</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Average Tips Per Day Card --}}
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="p-6">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0 p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                                        <x-heroicon-o-chart-bar class="h-8 w-8 text-orange-600 dark:text-orange-400" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Avg/Day</p>
                                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                                            ${{ number_format($summary['avg_tips_per_day'], 2) }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Average tips</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Employee Summary Table --}}
        @if (!empty($tipsData))
            <div class="fi-section">
                <div class="fi-section-content">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                        <div
                            class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                        Employee Summary
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                        {{ $summary['date_range'] }} • Aggregated employee performance and tip
                                        allocation
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200">
                                        {{ count($tipsData) }} Employees
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900/50">
                                    <tr>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                            Employee
                                        </th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                            Position
                                        </th>
                                        <th
                                            class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                            Days Worked
                                        </th>
                                        <th
                                            class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                            Total Hours
                                        </th>
                                        <th
                                            class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                            Avg Hours/Day
                                        </th>
                                        <th
                                            class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                            Total Points
                                        </th>
                                        <th
                                            class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                            Total Tips
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                    @foreach ($tipsData as $data)
                                        <tr
                                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ $data['employee_name'] }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200">
                                                    {{ $data['job_title'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $data['days_worked'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ number_format($data['total_hours'], 2) }}h
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ number_format($data['avg_hours_per_day'], 2) }}h
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ number_format($data['total_calculated_points'], 2) }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <div class="text-lg font-bold text-green-600 dark:text-green-400">
                                                    ${{ number_format($data['tip_amount'], 2) }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Daily Breakdown --}}
            @if (!empty($dailyBreakdown))
                <div class="fi-section">
                    <div class="fi-section-content">
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Daily Breakdown</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Day-by-day summary of tips
                                    pool activity</p>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                                    @foreach ($dailyBreakdown as $day)
                                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                            <div class="flex items-center justify-between mb-3">
                                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ \Carbon\Carbon::parse($day['date'])->format('M j, Y') }}
                                                </h4>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ \Carbon\Carbon::parse($day['date'])->format('l') }}
                                                </span>
                                            </div>
                                            <div class="space-y-2">
                                                <div class="flex justify-between items-center">
                                                    <span
                                                        class="text-xs text-gray-600 dark:text-gray-300">Employees:</span>
                                                    <span
                                                        class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $day['total_employees'] }}</span>
                                                </div>
                                                <div class="flex justify-between items-center">
                                                    <span
                                                        class="text-xs text-gray-600 dark:text-gray-300">Points:</span>
                                                    <span
                                                        class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ number_format($day['total_points'], 2) }}</span>
                                                </div>
                                                <div class="flex justify-between items-center">
                                                    <span class="text-xs text-gray-600 dark:text-gray-300">Tips:</span>
                                                    <span
                                                        class="text-sm font-bold text-green-600 dark:text-green-400">${{ number_format($day['total_tips'], 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="fi-section">
                <div class="fi-section-content">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-12 text-center">
                        <div
                            class="mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-6">
                            <x-heroicon-o-users class="h-12 w-12 text-gray-400 dark:text-gray-500" />
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">No eligible employees
                            found</h3>
                        <p class="text-gray-600 dark:text-gray-300 max-w-md mx-auto">
                            No employees with tip-eligible positions worked between
                            {{ \Carbon\Carbon::parse($startDate)->format('M j, Y') }} and
                            {{ \Carbon\Carbon::parse($endDate)->format('M j, Y') }}.
                            Try selecting a different date range or check if any positions have tips enabled.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Calculation Rules for Date Range --}}
        <div class="fi-section">
            <div class="fi-section-content">
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Date Range Calculation Rules
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">How tips are calculated and aggregated
                            across multiple days</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <x-heroicon-o-calendar-days class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-2">Daily
                                    Aggregation</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                                    Employee hours and points are calculated separately for each day, then aggregated
                                    across the selected date range.
                                    Each day follows the same 5+ hour rule for full points vs. proportional
                                    distribution.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 p-2 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <x-heroicon-o-check-circle class="h-6 w-6 text-green-600 dark:text-green-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-2">Employee
                                    Consolidation</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                                    Multiple time entries for the same employee on the same day are consolidated into
                                    total daily hours.
                                    Final report shows unique employees with their cumulative hours, points, and tips
                                    across all days.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                                <x-heroicon-o-banknotes class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-2">Tip
                                    Distribution</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                                    Total tips from all days in the range are pooled together and distributed
                                    proportionally
                                    based on each employee's total calculated points across all working days.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                <x-heroicon-o-chart-bar class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-2">Performance
                                    Metrics</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                                    The report includes total hours worked, days worked, and average hours per day to
                                    provide
                                    comprehensive performance insights across the selected time period.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
