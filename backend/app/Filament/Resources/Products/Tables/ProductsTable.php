<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Product;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumb')
                    ->label('Photo')
                    ->getStateUsing(fn (Product $record) => $record->primaryImageUrl())
                    ->checkFileExistence(false)
                    ->height(56)
                    ->square(),
                TextColumn::make('name')->label('Produit')->searchable()->sortable(),
                TextColumn::make('base_price')
                    ->label('Prix')
                    ->formatStateUsing(fn ($s) => number_format((int) $s, 0, ',', ' ').' FCFA'),
                TextColumn::make('stock_morocco')->label('Stock'),
                IconColumn::make('is_active')->boolean()->label('Actif'),
                IconColumn::make('is_featured')->boolean()->label('Vedette'),
            ])
            ->recordActions([EditAction::make()]);
    }
}
