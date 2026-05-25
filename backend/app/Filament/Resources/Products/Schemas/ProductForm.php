<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Informations')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                        TextInput::make('slug')->label('Slug')->required(),
                        Select::make('category')->label('Catégorie')->options([
                            'soiree' => 'Soirée', 'quotidien' => 'Quotidien', 'luxe' => 'Luxe', 'bandouliere' => 'Bandoulière',
                        ]),
                        TextInput::make('stock_morocco')->label('Stock Maroc')->numeric()->default(0)->required(),
                        Textarea::make('description')->label('Description')->columnSpanFull(),
                    ]),
                Section::make('Prix (FCFA)')->columnSpanFull()->columns(2)->schema([
                    TextInput::make('base_price')->label('Prix')->required()->numeric()->suffix('FCFA'),
                    TextInput::make('compare_at_price')->label('Prix barré')->numeric()->suffix('FCFA'),
                ]),
                Section::make('Photos produit')
                    ->description('Aperçu et remplacement des images — enregistrées dans /images/products/')
                    ->columnSpanFull()
                    ->schema([
                        FileUpload::make('images')
                            ->label('Galerie')
                            ->disk('product_images')
                            ->directory('')
                            ->visibility('public')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->panelLayout('grid')
                            ->imageEditor()
                            ->maxFiles(8)
                            ->imagePreviewHeight('150')
                            ->formatStateUsing(fn ($state, ?Product $record) => $record?->imagesForUpload() ?? [])
                            ->dehydrateStateUsing(fn (?array $state) => Product::pathsFromUpload($state)),
                    ]),
                Section::make('Visibilité')->columnSpanFull()->columns(2)->schema([
                    Toggle::make('is_active')->label('Actif')->default(true),
                    Toggle::make('is_featured')->label('En vedette')->default(false),
                    TextInput::make('average_rating')->disabled()->numeric()->default(0),
                    TextInput::make('reviews_count')->disabled()->numeric()->default(0),
                ]),
            ]);
    }
}
