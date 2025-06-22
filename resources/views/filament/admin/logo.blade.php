{{-- Custom Logo for Tips Manager Admin Panel --}}
<div class="flex items-center space-x-3">
    {{-- Tips Icon with fallback --}}
    <div class="flex-shrink-0">
        @if (function_exists('svg'))
            @svg('hugeicons-tips', 'h-8 w-8 text-primary-600 dark:text-primary-400')
        @else
            {{-- Fallback icon using Heroicon --}}
            <svg class="h-8 w-8 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
            </svg>
        @endif
    </div>

    {{-- App Name --}}
    <div class="flex flex-col">
        <span class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">
            Tips Manager
        </span>
        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">
            By Costeno Group
        </span>
    </div>
</div>
