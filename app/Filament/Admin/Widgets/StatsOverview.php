<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '10s';

    protected static ?int $sort = 0;
    
    protected function getStats(): array
    {
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total');
        $todayrevenue = Order::where('payment_status', 'paid')->whereDate('created_at', today())->sum('total');

        $totalOrders = Order::count();
        $pendingOrsers = Order::where('status', 'pending')->count();

        $totalCustomers = Customer::count();
        $newCustomersThisMonth = Customer::whereMonth('created_at', now())
            ->whereYear('created_at', now())
            ->count();

        $lowStockProducts = Product::lowStock()->count();

        return [
            Stat::make('Total Revenue', number_format($totalRevenue, 2))
                ->description('Today $' . number_format($todayrevenue, 2))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Total Orders', $totalOrders)
                ->description($pendingOrsers . ' Pending')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('warning')
                ->url(route('filament.admin.resources.orders.index')),
            Stat::make('Total Customer', $totalCustomers)
                ->description($newCustomersThisMonth . ' new this month')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info')
                ->url(route('filament.admin.resources.customers.index')),
            Stat::make('Low Stock Alert', $lowStockProducts)
                ->description('Product running low')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger')
                ->url(route('filament.admin.resources.products.index')),
        ];
    }
}