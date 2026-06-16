<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Orders\Concerns\HasOrderQuickActions;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestOrdersWidget extends TableWidget
{
    use HasOrderQuickActions;

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Dernières commandes';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->latest()->limit(8))
            ->columns([
                TextColumn::make('reference')
                    ->label('Réf.')
                    ->searchable(),
                TextColumn::make('customer_name')
                    ->label('Client'),
                TextColumn::make('shipping_city')
                    ->label('Ville'),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label() ?? $state),
                TextColumn::make('total')
                    ->label('Total')
                    ->formatStateUsing(fn ($state) => number_format((int) $state, 0, ',', ' ').' FCFA'),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                ...self::orderQuickActions(),
                Action::make('edit')
                    ->label('Voir')
                    ->url(fn (Order $record) => OrderResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated(false);
    }
}
