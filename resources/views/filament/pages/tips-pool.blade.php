<x-filament-panels::page>
    <div class="space-y-10 p-4 sm:p-6">
        {{-- Date Filter Form --}}
        <div class="fi-section mb-4">
            <div
                class="fi-section-content p-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    Select Report Date
                </h2>
                {{ $this->form }}
            </div>
        </div>

        {{-- Summary Cards Grid - 4 Columns --}}
        @if (!empty($summary))
            <div class="fi-section">
                <div class="fi-section-content">
                    <div class="mb-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                        {{-- Total Employees Card --}}
                        <div
                            class=" bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
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
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300 mr-2">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                AM: {{ $summary['am_employees'] }}
                                            </span>
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                                                </svg>
                                                PM: {{ $summary['pm_employees'] }}
                                            </span>
                                        </p>
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
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                            AM: {{ number_format($summary['am_total_points'], 2) }} |
                                            PM: {{ number_format($summary['pm_total_points'], 2) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Available Tips Card --}}
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="p-6">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                                        <x-heroicon-o-banknotes class="h-8 w-8 text-yellow-600 dark:text-yellow-400" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Available
                                            Tips</p>
                                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                                            ${{ number_format($summary['total_tips_amount'], 2) }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                            AM: ${{ number_format($summary['am_tips_amount'], 2) }} |
                                            PM: ${{ number_format($summary['pm_tips_amount'], 2) }}
                                        </p>
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
                                            Avg:
                                            ${{ number_format(($summary['am_tip_per_point'] + $summary['pm_tip_per_point']) / 2, 2) }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                            AM: ${{ number_format($summary['am_tip_per_point'], 2) }} |
                                            PM: ${{ number_format($summary['pm_tip_per_point'], 2) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- No Tips Alert --}}
        @if (
            !empty($summary) &&
                $summary['total_tips_amount'] == 0 &&
                isset($summary['eligible_employees_count']) &&
                $summary['eligible_employees_count'] > 0)
            <div class="fi-section">
                <div class="fi-section-content">
                    <div
                        class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-amber-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-amber-800 dark:text-amber-200 mb-2">
                                    No Tips Registered for This Date
                                </h3>
                                <div class="text-amber-700 dark:text-amber-300 space-y-2">
                                    <p>
                                        <strong>{{ $summary['eligible_employees_count'] }}</strong> employees with
                                        tip-eligible positions worked on
                                        <strong>{{ \Carbon\Carbon::parse($selectedDate)->format('F j, Y') }}</strong>,
                                        but no tips have been registered for distribution.
                                    </p>
                                    <div class="mt-4 p-4 bg-amber-100 dark:bg-amber-900/40 rounded-lg">
                                        <p class="font-medium mb-2">To distribute tips for this date:</p>
                                        <ol class="list-decimal list-inside space-y-1 text-sm">
                                            <li>Go to <strong>Daily Tips</strong> section</li>
                                            <li>Add tip amounts for AM and/or PM shifts</li>
                                            <li>Return here to generate the distribution report</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Tips Distribution Table --}}
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
                                        Tips Distribution
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                        {{ \Carbon\Carbon::parse($selectedDate)->format('F j, Y') }} • Employee points
                                        and tip allocation breakdown
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
                                            Shift
                                        </th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                            In Date
                                        </th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                            Out Date
                                        </th>
                                        <th
                                            class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                            Unpaid Break
                                        </th>
                                        <th
                                            class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                            Hours
                                        </th>
                                        <th
                                            class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                            Base Points
                                        </th>
                                        <th
                                            class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                            Calculated Points
                                        </th>
                                        <th
                                            class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                            Status
                                        </th>
                                        <th
                                            class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                            Tip Amount
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
                                                <span
                                                    class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full 
                                                    {{ $data['shift'] === 'AM' ? 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300' : 'bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300' }}">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        @if ($data['shift'] === 'AM')
                                                            {{-- Sun icon for AM --}}
                                                            <path fill-rule="evenodd"
                                                                d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                                                clip-rule="evenodd" />
                                                        @else
                                                            {{-- Moon icon for PM --}}
                                                            <path
                                                                d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                                                        @endif
                                                    </svg>
                                                    {{ $data['shift'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $data['in_date'] ? \Carbon\Carbon::parse($data['in_date'])->format('m/d/Y H:i') : '-' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $data['out_date'] ? \Carbon\Carbon::parse($data['out_date'])->format('m/d/Y H:i') : '-' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ number_format($data['unpaid_break_time'], 2) }}h
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ number_format($data['hours_worked'], 2) }}h
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ number_format($data['job_position_points'], 2) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <div class="space-y-1">
                                                    <div
                                                        class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                        {{ number_format($data['calculated_points'], 2) }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $data['percentage'] }}% of base
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                @if ($data['qualifies_for_full_points'])
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                                                        <x-heroicon-m-check-circle class="w-4 h-4 mr-1" />
                                                        Full Points
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200">
                                                        <x-heroicon-m-clock class="w-4 h-4 mr-1" />
                                                        Proportional
                                                    </span>
                                                @endif
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
                            No employees with tip-eligible positions worked on
                            {{ \Carbon\Carbon::parse($selectedDate)->format('F j, Y') }}.
                            Try selecting a different date or check if any positions have tips enabled.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Calculation Rules --}}
        <div class="fi-section">
            <div class="fi-section-content mt-4">
                <div
                    class="mt-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Calculation Rules</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">How tips are calculated and
                            distributed
                        </p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 p-2 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <x-heroicon-o-check-circle class="h-6 w-6 text-green-600 dark:text-green-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-2">Full Points
                                    (5+ hours)</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                                    Employees working 5 or more hours receive the full point value assigned to their
                                    position.
                                    This ensures full-time workers get their complete tip allocation.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                                <x-heroicon-o-calculator class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-2">Proportional
                                    Points (&lt;5 hours)</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                                    Points calculated using rule of 3: <code
                                        class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs">(hours worked ÷
                                        5) × position points</code>.
                                    Part-time workers receive tips proportional to their hours.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <x-heroicon-o-banknotes class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-2">Tip
                                    Distribution</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                                    Daily tips are distributed proportionally based on calculated points. Each point
                                    earns the same amount,
                                    ensuring fair distribution across all eligible employees.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                <x-heroicon-o-user-group class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-2">Eligibility
                                    Requirements</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                                    Only employees in positions with "Applies for Tips" enabled are included in the
                                    distribution.
                                    Check job position settings to modify tip eligibility.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
