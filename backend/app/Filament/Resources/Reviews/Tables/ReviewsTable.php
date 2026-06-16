<?php

namespace App\Filament\Resources\Reviews\Tables;

use App\Models\Review;
use App\Services\ReviewService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('product.name')
                    ->label('Produit')
                    ->searchable(),
                TextColumn::make('author_name')
                    ->label('Auteur')
                    ->searchable(),
                TextColumn::make('rating')
                    ->label('Note')
                    ->formatStateUsing(fn ($s) => str_repeat('★', (int) $s).str_repeat('☆', 5 - (int) $s))
                    ->sortable(),
                TextColumn::make('comment')
                    ->label('Commentaire')
                    ->limit(40)
                    ->toggleable(),
                IconColumn::make('is_verified_purchase')
                    ->label('Achat vérifié')
                    ->boolean(),
                IconColumn::make('is_approved')
                    ->label('Publié')
                    ->boolean(),
                TextColumn::make('order.reference')
                    ->label('Commande')
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_approved')->label('Publié'),
                TernaryFilter::make('is_verified_purchase')->label('Achat vérifié'),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Publier')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->visible(fn (Review $record) => ! $record->is_approved)
                    ->action(function (Review $record) {
                        app(ReviewService::class)->approve($record);
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
