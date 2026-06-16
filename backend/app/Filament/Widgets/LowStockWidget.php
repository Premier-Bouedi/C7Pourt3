<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LowStockWidget extends TableWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Stock Maroc faible (≤ 5)';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->where('is_active', true)
                    ->where('stock_morocco', '<=', 5)
                    ->orderBy('stock_morocco')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Produit')
                    ->searchable(),
                TextColumn::make('category')
                    ->label('Catégorie')
                    ->badge(),
                TextColumn::make('stock_morocco')
                    ->label('Stock Maroc')
                    ->color(fn ($state) => (int) $state <= 2 ? 'danger' : 'warning'),
                TextColumn::make('base_price')
                    ->label('Prix')
                    ->formatStateUsing(fn ($state) => number_format((int) $state, 0, ',', ' ').' FCFA'),
            ])
            ->recordActions([
                Action::make('edit')
                    ->label('Modifier')
                    ->url(fn (Product $record) => ProductResource::getUrl('edit', ['record' => $record])),
            ])
            ->emptyStateHeading('Stock OK')
            ->emptyStateDescription('Tous les produits ont plus de 5 unités en stock Maroc.')
            ->paginated(false);
    }
}
