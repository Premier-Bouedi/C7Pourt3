<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Enums\PaymentStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('order_id')
                    ->relationship('order', 'id')
                    ->required(),
                TextInput::make('amount_due')
                    ->required()
                    ->numeric(),
                TextInput::make('amount_collected')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('payment_method')
                    ->required()
                    ->default('cod_cash'),
                Select::make('status')
                    ->options(PaymentStatus::class)
                    ->default('pending')
                    ->required(),
                FileUpload::make('proof_image')
                    ->image(),
                Textarea::make('admin_notes')
                    ->columnSpanFull(),
                TextInput::make('validated_by')
                    ->numeric(),
                DateTimePicker::make('collected_at'),
            ]);
    }
}
