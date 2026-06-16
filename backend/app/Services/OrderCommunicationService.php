<?php

namespace App\Services;

use App\Enums\OrderIssueReason;
use App\Enums\OrderStatus;
use App\Mail\DeliveryTodayMail;
use App\Mail\OrderIssueAlertMail;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;

class OrderCommunicationService
{
    public function __construct(
        private readonly WhatsAppService $whatsApp,
    ) {}

    /**
     * Pack « Livraison aujourd'hui » : email client + lien WhatsApp pré-rempli.
     *
     * @return array{whatsapp_url: string, email_sent: bool}
     */
    /**
     * Pack Message 2 — Confirmation & planification (WhatsApp + email luxe).
     *
     * @return array{whatsapp_url: string, email_sent: bool}
     */
    public function sendDeliveryTodayPack(Order $order, string $timeFrom = '10h', string $timeTo = '14h'): array
    {
        $order->load('items');
        $order->update([
            'estimated_delivery_at' => now()->addDay()->toDateString(),
            'confirmed_at' => $order->confirmed_at ?? now(),
            'arrived_gabon_at' => $order->arrived_gabon_at ?? now(),
            'status' => $order->status === OrderStatus::Delivered
                ? OrderStatus::Delivered
                : OrderStatus::Confirmed,
        ]);

        $message = $this->whatsApp->confirmationPlanningMessage($order, null, $timeFrom, $timeTo);
        $emailSent = $this->sendMail($order, new DeliveryTodayMail($order->fresh()));

        return [
            'whatsapp_url' => $this->whatsApp->customerUrl($order, $message),
            'email_sent' => $emailSent,
        ];
    }

    /** Pack Message 3 — Demande d'avis post-livraison. */
    public function sendReviewRequestPack(Order $order): array
    {
        $message = $this->whatsApp->reviewRequestMessage($order);
        $emailSent = $this->sendMail($order, new \App\Mail\ReviewInvitationMail($order));

        return [
            'whatsapp_url' => $this->whatsApp->customerUrl($order, $message),
            'email_sent' => $emailSent,
        ];
    }

    /**
     * Pack « Alerte client » : email + WhatsApp selon le motif choisi.
     *
     * @return array{whatsapp_url: string, email_sent: bool}
     */
    public function sendIssueAlertPack(Order $order, OrderIssueReason $reason, ?string $details = null): array
    {
        $label = $reason->label();
        $note = "[Alerte {$label}]".($details ? " {$details}" : '');
        $order->update([
            'notes' => trim(($order->notes ? $order->notes."\n" : '').$note),
        ]);

        $message = $this->whatsApp->issueAlertMessage($order, $label, $details);
        $emailSent = $this->sendMail(
            $order,
            new OrderIssueAlertMail($order->fresh(), $label, $details),
        );

        return [
            'whatsapp_url' => $this->whatsApp->customerUrl($order, $message),
            'email_sent' => $emailSent,
        ];
    }

    private function sendMail(Order $order, $mailable): bool
    {
        if (! $order->customer_email) {
            return false;
        }

        Mail::to($order->customer_email)->send($mailable);

        return true;
    }
}
