<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;

class WhatsAppService
{
    public function number(): string
    {
        return preg_replace('/\D/', '', config('c7pourt3.whatsapp_number', '24100000000'));
    }

    /** Lien vers le numéro C7Pourt3 (boutique). */
    public function url(string $message): string
    {
        return 'https://wa.me/'.$this->number().'?text='.rawurlencode($message);
    }

    public function general(): string
    {
        return $this->url('Bonjour C7Pourt3, je souhaite des informations sur vos sacs de luxe.');
    }

    /**
     * Message 1 — Initiation commande (clic « Acheter » / Commander via WhatsApp).
     */
    public function orderInitiation(string $productName, int $priceFcfa, string $city = 'Libreville'): string
    {
        $price = number_format($priceFcfa, 0, ',', ' ');

        return $this->url(
            "Bonjour C7Pourt3, je souhaite commander le sac {$productName} au prix de {$price} FCFA. "
            ."Voici mes informations pour la livraison à {$city} : [Nom/Prénom] - [Quartier] - [Téléphone]."
        );
    }

    public function cartHelp(int $items, int $totalFcfa): string
    {
        return $this->url(
            "Bonjour C7Pourt3, j'ai {$items} article(s) dans mon panier (total "
            .number_format($totalFcfa, 0, ',', ' ')." FCFA). "
            .'Voici mes informations pour la livraison à Libreville : [Nom/Prénom] - [Quartier] - [Téléphone].'
        );
    }

    public function checkoutHelp(): string
    {
        return $this->url(
            'Bonjour C7Pourt3, je suis en train de finaliser ma commande sur le site et j\'ai une question.'
        );
    }

    /** Message 2 — Confirmation & planification livraison (admin → cliente). */
    public function confirmationPlanningMessage(
        Order $order,
        ?string $productName = null,
        string $timeFrom = '10h',
        string $timeTo = '14h',
    ): string {
        $name = $this->firstName($order->customer_name);
        $model = $productName ?? $this->primaryProductName($order);
        $price = number_format($order->total, 0, ',', ' ');

        return "Bonjour {$name}, c'est l'équipe C7Pourt3. ✨ "
            ."Nous avons bien préparé votre commande pour le modèle {$model}. "
            ."Notre livreur passera demain entre {$timeFrom} et {$timeTo}. "
            ."Le montant total de {$price} FCFA sera à régler en espèces à la livraison. "
            .'Un message vous sera envoyé 30 min avant son arrivée. Merci pour votre confiance !';
    }

    /**
     * Message 3 — Suivi post-achat / demande d'avis.
     */
    public function reviewRequestMessage(Order $order): string
    {
        $name = $this->firstName($order->customer_name);
        $link = url('/avis?ref='.$order->reference);

        return "Bonjour {$name}, nous espérons que votre nouveau sac C7Pourt3 vous plaît et fait sensation ! ✨ "
            .'Votre avis compte énormément pour notre jeune marque. '
            .'Pourriez-vous prendre 1 minute pour nous laisser un commentaire et une note sur notre site ? '
            ."Voici le lien direct : {$link} Merci infiniment !";
    }

    public function customerUrl(Order $order, string $message): string
    {
        $phone = preg_replace('/\D/', '', $order->customer_phone);

        return 'https://wa.me/'.$phone.'?text='.rawurlencode($message);
    }

    public function deliveryTodayMessage(Order $order, string $timeFrom = '10h', string $timeTo = '14h'): string
    {
        return $this->confirmationPlanningMessage($order, null, $timeFrom, $timeTo);
    }

    public function issueAlertMessage(Order $order, string $reasonLabel, ?string $details = null): string
    {
        $msg = "Bonjour {$this->firstName($order->customer_name)}, ici C7Pourt3 concernant votre commande {$order->reference}. "
            ."Nous vous informons : {$reasonLabel}.";

        if ($details) {
            $msg .= " Détail : {$details}.";
        }

        return $msg.' Merci de nous répondre sur WhatsApp pour la suite.';
    }

    public function orderConfirmed(Order $order): string
    {
        return $this->customerUrl(
            $order,
            "Bonjour, je viens de passer la commande {$order->reference} sur C7Pourt3. Merci !",
        );
    }

    public function satisfaction(Order $order): string
    {
        return $this->customerUrl($order, $this->reviewRequestMessage($order));
    }

    public function trackOrder(string $reference): string
    {
        return $this->url("Bonjour C7Pourt3, je souhaite suivre ma commande {$reference}.");
    }

    private function firstName(string $fullName): string
    {
        return explode(' ', trim($fullName))[0] ?: $fullName;
    }

    private function primaryProductName(Order $order): string
    {
        $order->loadMissing('items');

        /** @var OrderItem|null $item */
        $item = $order->items->first();

        return $item?->product_name ?? 'votre sac';
    }
}
