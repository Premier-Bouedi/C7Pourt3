<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\OrderStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('reference')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name'),
                TextInput::make('customer_name')
                    ->required(),
                TextInput::make('customer_email')
                    ->email(),
                TextInput::make('customer_phone')
                    ->tel()
                    ->required(),
                TextInput::make('shipping_city')
                    ->required(),
                Textarea::make('shipping_address')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(OrderStatus::class)
                    ->default('pending')
                    ->required(),
                TextInput::make('subtotal')
                    ->required()
                    ->numeric(),
                TextInput::make('shipping_fee')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total')
                    ->required()
                    ->numeric(),
                TextInput::make('currency')
                    ->required()
                    ->default('XAF'),
                DateTimePicker::make('confirmed_at'),
                DateTimePicker::make('shipped_at'),
                DateTimePicker::make('arrived_gabon_at'),
                DateTimePicker::make('delivered_at'),
                DatePicker::make('estimated_delivery_at'),
            ]);
    }
}
