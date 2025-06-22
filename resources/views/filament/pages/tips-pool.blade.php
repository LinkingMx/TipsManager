<x-filament-panels::page>
    <div class="fi-page-content">
        <div class="fi-section">
            <div class="fi-section-content space-y-6">
                {{-- Header Section --}}
                <div class="fi-section-header">
                    <div class="flex items-center gap-3">
                        <div
                            class="fi-icon-wrapper flex h-12 w-12 items-center justify-center rounded-xl bg-primary-50 dark:bg-primary-900/10">
                            <x-heroicon-o-currency-dollar class="h-6 w-6 text-primary-600 dark:text-primary-400" />
                        </div>
                        <div>
                            <h3
                                class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                Tips Pool Management
                            </h3>
                            <p class="fi-section-header-description text-sm text-gray-500 dark:text-gray-400">
                                Manage and distribute tips among team members based on shifts and positions.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Coming Soon Content --}}
                <div class="fi-section-content">
                    <div
                        class="rounded-xl border border-gray-200 bg-white p-8 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div class="text-center">
                            <div
                                class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
                                <x-heroicon-o-cog class="h-10 w-10 text-gray-400 dark:text-gray-500" />
                            </div>

                            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">
                                Coming Soon
                            </h3>

                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                The Tips Pool functionality is currently under development. This page will soon include:
                            </p>

                            <div class="mt-6 space-y-2">
                                <div class="flex items-center justify-center text-sm text-gray-600 dark:text-gray-300">
                                    <x-heroicon-m-check-circle class="mr-2 h-4 w-4 text-green-500" />
                                    Automatic tip distribution based on job positions
                                </div>
                                <div class="flex items-center justify-center text-sm text-gray-600 dark:text-gray-300">
                                    <x-heroicon-m-check-circle class="mr-2 h-4 w-4 text-green-500" />
                                    Shift-based tip allocation
                                </div>
                                <div class="flex items-center justify-center text-sm text-gray-600 dark:text-gray-300">
                                    <x-heroicon-m-check-circle class="mr-2 h-4 w-4 text-green-500" />
                                    Point-based distribution system
                                </div>
                                <div class="flex items-center justify-center text-sm text-gray-600 dark:text-gray-300">
                                    <x-heroicon-m-check-circle class="mr-2 h-4 w-4 text-green-500" />
                                    Real-time tip pool calculations
                                </div>
                                <div class="flex items-center justify-center text-sm text-gray-600 dark:text-gray-300">
                                    <x-heroicon-m-check-circle class="mr-2 h-4 w-4 text-green-500" />
                                    Detailed reporting and analytics
                                </div>
                            </div>

                            <div class="mt-8">
                                <div
                                    class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 dark:bg-blue-900/20 dark:text-blue-400">
                                    <x-heroicon-m-clock class="mr-1 h-3 w-3" />
                                    In Development
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Stats Placeholder --}}
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div
                        class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <x-heroicon-o-banknotes class="h-8 w-8 text-green-600" />
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pool</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">$0.00</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <x-heroicon-o-users class="h-8 w-8 text-blue-600" />
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Staff</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">0</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <x-heroicon-o-clock class="h-8 w-8 text-purple-600" />
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Shift</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">None</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <x-heroicon-o-calculator class="h-8 w-8 text-orange-600" />
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Distributions</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">0</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
