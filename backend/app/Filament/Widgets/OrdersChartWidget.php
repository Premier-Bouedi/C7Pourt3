<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class OrdersChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Commandes — 7 derniers jours';

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'md' => 2,
        'xl' => 2,
    ];

    protected function getData(): array
    {
        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $labels[] = $day->translatedFormat('D d/m');
            $data[] = Order::whereDate('created_at', $day)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Commandes',
                    'data' => $data,
                    'borderColor' => '#44403c',
                    'backgroundColor' => 'rgba(68, 64, 60, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
