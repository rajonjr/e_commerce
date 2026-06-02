<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Revenue Chart';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public ?string $filter = 'week';

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        $data = Trend::model(Order::class)
            ->between(
                start: match ($activeFilter) {
                    'week' => Carbon::now()->startOfWeek(),
                    'month' => Carbon::now()->startOfMonth(),
                    'year' => Carbon::now()->startOfYear(),
                    default => Carbon::now()
                },
                end: Carbon::now(),
            )
            ->perWeek()
            ->sum('total');

        return [
            'dataset' => [
                'label' => 'revenue',
                'data' => $data->map(fn(TrendValue $value) => $value->aggregate)
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date)
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            'week' => 'Last Week',
            'month' => 'Last Month',
            'year' => 'Last Year',
        ];
    }
}