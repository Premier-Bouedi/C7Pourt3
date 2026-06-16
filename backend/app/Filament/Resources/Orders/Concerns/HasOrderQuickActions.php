<?php

namespace App\Filament\Resources\Orders\Concerns;

use App\Enums\OrderIssueReason;
use App\Models\Order;
use App\Services\OrderCommunicationService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;

trait HasOrderQuickActions
{
    /** @return array<int, Action> */
    protected static function orderQuickActions(): array
    {
        return [
            Action::make('scheduleDelivery')
                ->label('Planifier livraison')
                ->icon('heroicon-o-truck')
                ->color('success')
                ->modalHeading('Confirmation & planification (Message 2)')
                ->modalDescription('Envoie le pack WhatsApp + email : livreur demain, montant COD, préavis 30 min.')
                ->modalSubmitActionLabel('Envoyer le pack')
                ->form([
                    Select::make('time_slot')
                        ->label('Créneau de livraison (demain)')
                        ->options([
                            '10h|14h' => 'Entre 10h et 14h',
                            '14h|18h' => 'Entre 14h et 18h',
                            '09h|12h' => 'Entre 9h et 12h',
                            '16h|19h' => 'Entre 16h et 19h',
                        ])
                        ->default('10h|14h')
                        ->required()
                        ->native(false),
                ])
                ->action(function (Order $record, array $data): void {
                    [$from, $to] = explode('|', $data['time_slot']);
                    $result = app(OrderCommunicationService::class)->sendDeliveryTodayPack($record, $from, $to);
                    static::notifyPackSent('Pack planification envoyé', $result);
                }),

            Action::make('requestReview')
                ->label('Demander avis')
                ->icon('heroicon-o-star')
                ->color('warning')
                ->visible(fn (Order $record) => $record->status?->value === 'delivered')
                ->requiresConfirmation()
                ->modalHeading('Demande d\'avis (Message 3)')
                ->modalDescription('WhatsApp + email avec lien vers la page avis (achat vérifié).')
                ->modalSubmitActionLabel('Envoyer')
                ->action(function (Order $record): void {
                    $result = app(OrderCommunicationService::class)->sendReviewRequestPack($record);
                    static::notifyPackSent('Demande d\'avis envoyée', $result);
                }),

            Action::make('reportIssue')
                ->label('Signaler souci')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->modalHeading('Alerte client')
                ->modalDescription('Choisissez le motif et envoyez le pack alerte (email + WhatsApp).')
                ->modalSubmitActionLabel('Envoyer l\'alerte')
                ->form([
                    Select::make('reason')
                        ->label('Motif du souci')
                        ->options(OrderIssueReason::options())
                        ->required()
                        ->native(false),
                    Textarea::make('details')
                        ->label('Précisions (optionnel)')
                        ->rows(3)
                        ->maxLength(500),
                ])
                ->action(function (Order $record, array $data): void {
                    $reason = OrderIssueReason::from($data['reason']);
                    $result = app(OrderCommunicationService::class)->sendIssueAlertPack(
                        $record,
                        $reason,
                        $data['details'] ?? null,
                    );

                    $notification = Notification::make()
                        ->title('Alerte client envoyée')
                        ->body("Motif : {$reason->label()}.")
                        ->warning();

                    $notification->actions([
                        Action::make('openWhatsApp')
                            ->label('Ouvrir WhatsApp client')
                            ->url($result['whatsapp_url'])
                            ->openUrlInNewTab(),
                    ]);

                    $notification->send();
                }),
        ];
    }

    /** @param  array{whatsapp_url: string, email_sent: bool}  $result */
    protected static function notifyPackSent(string $title, array $result): void
    {
        $notification = Notification::make()
            ->title($title)
            ->body(
                ($result['email_sent'] ? 'Email luxe envoyé. ' : 'Pas d\'email client. ')
                .'Cliquez pour ouvrir WhatsApp vers la cliente.'
            )
            ->success();

        $notification->actions([
            Action::make('openWhatsApp')
                ->label('Ouvrir WhatsApp client')
                ->url($result['whatsapp_url'])
                ->openUrlInNewTab(),
        ]);

        $notification->send();
    }
}
