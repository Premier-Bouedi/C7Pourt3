<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Filament\Resources\Orders\Concerns\HasOrderQuickActions;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
    use HasOrderQuickActions;
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('customer_name')
                    ->searchable(),
                TextColumn::make('customer_email')
                    ->searchable(),
                TextColumn::make('customer_phone')
                    ->searchable(),
                TextColumn::make('shipping_city')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label() ?? $state)
                    ->searchable(),
                TextColumn::make('subtotal')
                    ->label('Sous-total')
                    ->formatStateUsing(fn ($s) => number_format((int) $s, 0, ',', ' ').' FCFA')
                    ->sortable(),
                TextColumn::make('shipping_fee')
                    ->label('Livraison')
                    ->formatStateUsing(fn ($s) => number_format((int) $s, 0, ',', ' ').' FCFA')
                    ->sortable(),
                TextColumn::make('total')
                    ->label('Total')
                    ->formatStateUsing(fn ($s) => number_format((int) $s, 0, ',', ' ').' FCFA')
                    ->sortable(),
                TextColumn::make('currency')
                    ->searchable(),
                TextColumn::make('confirmed_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('shipped_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('arrived_gabon_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('delivered_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('estimated_delivery_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ...self::orderQuickActions(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
