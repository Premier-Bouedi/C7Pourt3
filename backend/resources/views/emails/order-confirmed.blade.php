@extends('emails.layout')

@section('content')
    @php
        $item = $order->items->first();
        $productName = $item?->product_name ?? 'Sac C7Pourt3';
    @endphp
    <p style="margin:0 0 8px;font-size:12px;letter-spacing:0.2em;color:#d4af37;text-transform:uppercase;">Commande validée ✨</p>
    <p style="margin:0 0 24px;font-size:18px;color:#fafaf9;">Chère cliente,</p>
    <p style="margin:0 0 20px;">Nous vous remercions pour votre commande chez <strong style="color:#d4af37;">C7Pourt3</strong>. Nous préparons votre pièce d'exception avec le plus grand soin.</p>

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#292524;border:1px solid #44403c;margin:28px 0;">
        <tr>
            <td style="padding:24px;">
                <p style="margin:0 0 16px;font-size:11px;letter-spacing:0.15em;color:#a8a29e;text-transform:uppercase;">Détails de la commande</p>
                <p style="margin:0 0 8px;"><span style="color:#a8a29e;">Article :</span> <strong>{{ $productName }}</strong></p>
                <p style="margin:0 0 8px;"><span style="color:#a8a29e;">Référence :</span> {{ $order->reference }}</p>
                <p style="margin:0 0 8px;"><span style="color:#a8a29e;">Mode de règlement :</span> Paiement à la livraison (COD)</p>
                <p style="margin:0;"><span style="color:#a8a29e;">Montant :</span> <strong style="color:#d4af37;">{{ number_format($order->total, 0, ',', ' ') }} FCFA</strong></p>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 20px;">Notre équipe logistique au Gabon va prendre contact avec vous par <strong>WhatsApp</strong> pour coordonner la livraison à votre domicile ou bureau.</p>
    <p style="margin:0 0 8px;color:#fafaf9;">À très bientôt,</p>
    <p style="margin:0;font-style:italic;color:#d4af37;">L'équipe C7Pourt3</p>
@endsection
