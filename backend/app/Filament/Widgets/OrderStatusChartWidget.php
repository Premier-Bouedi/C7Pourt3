<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrderStatusChartWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Statuts des commandes';

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'md' => 1,
        'xl' => 1,
    ];

    protected function getData(): array
    {
        $labels = [];
        $data = [];
        $colors = [
            '#a8a29e',
            '#57534e',
            '#78716c',
            '#44403c',
            '#292524',
            '#ef4444',
        ];

        foreach (OrderStatus::cases() as $i => $status) {
            $count = Order::where('status', $status)->count();
            if ($count > 0) {
                $labels[] = $status->label();
                $data[] = $count;
            }
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
