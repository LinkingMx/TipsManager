<?php

namespace App\Filament\Widgets;

use App\Models\DailyTip;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class DailyTipsChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Daily Tips Trend';

    protected static ?string $description = 'Last 10 registered daily tip records';

    protected static ?int $sort = -1;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '200px';

    protected function getData(): array
    {
        $dailyTips = DailyTip::orderBy('date', 'desc')
            ->limit(10)
            ->get()
            ->reverse()
            ->values();

        $labels = [];
        $data = [];

        foreach ($dailyTips as $tip) {
            $labels[] = Carbon::parse($tip->date)->format('M j');
            $data[] = (float) $tip->amount;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Daily Tips',
                    'data' => $data,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => '#3b82f6',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                    'labels' => [
                        'padding' => 10,
                        'boxWidth' => 12,
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'maxTicksLimit' => 5,
                    ],
                ],
                'x' => [
                    'ticks' => [
                        'maxTicksLimit' => 8,
                    ],
                ],
            ],
        ];
    }
}
