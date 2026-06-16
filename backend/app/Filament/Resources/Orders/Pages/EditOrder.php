<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\Concerns\HasOrderQuickActions;
use App\Filament\Resources\Orders\OrderResource;
use App\Services\OrderService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    use HasOrderQuickActions;

    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ...self::orderQuickActions(),
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        if ($this->record->status === OrderStatus::Delivered) {
            app(OrderService::class)->markDelivered($this->record->fresh());
        }
    }
}
