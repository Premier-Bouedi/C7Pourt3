<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Payments\PaymentResource;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Reviews\ReviewResource;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Review;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class C7StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Vue d\'ensemble';

    protected function getStats(): array
    {
        $revenue = (int) Order::where('status', '!=', OrderStatus::Cancelled)->sum('total');
        $collected = (int) Payment::where('status', PaymentStatus::Collected)->sum('amount_collected');
        $pendingOrders = Order::where('status', OrderStatus::Pending)->count();
        $inTransit = Order::whereIn('status', [
            OrderStatus::Confirmed,
            OrderStatus::ShippedMorocco,
            OrderStatus::ArrivedGabon,
        ])->count();

        return [
            Stat::make('Produits actifs', Product::where('is_active', true)->count())
                ->description(Product::where('is_featured', true)->count().' en vedette')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('gray')
                ->url(ProductResource::getUrl('index')),
            Stat::make('Commandes', Order::count())
                ->description($pendingOrders.' en attente')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color($pendingOrders > 0 ? 'warning' : 'success')
                ->url(OrderResource::getUrl('index')),
            Stat::make('En livraison', $inTransit)
                ->description('Maroc → Gabon')
                ->descriptionIcon('heroicon-m-truck')
                ->color('info')
                ->url(OrderResource::getUrl('index')),
            Stat::make('Chiffre d\'affaires', $this->fcfa($revenue))
                ->description('Commandes non annulées')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make('Encaissé COD', $this->fcfa($collected))
                ->description(Payment::where('status', PaymentStatus::Pending)->count().' paiements en attente')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->url(PaymentResource::getUrl('index')),
            Stat::make('Avis à modérer', Review::where('is_approved', false)->count())
                ->description(Review::where('is_approved', true)->count().' publiés')
                ->descriptionIcon('heroicon-m-star')
                ->color(Review::where('is_approved', false)->count() > 0 ? 'warning' : 'gray')
                ->url(ReviewResource::getUrl('index')),
        ];
    }

    private function fcfa(int $amount): string
    {
        return number_format($amount, 0, ',', ' ').' FCFA';
    }
}
