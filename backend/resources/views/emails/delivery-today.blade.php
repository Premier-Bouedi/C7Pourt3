@extends('emails.layout')

@section('content')
    @php $item = $order->items->first(); @endphp
    <p style="margin:0 0 8px;font-size:12px;letter-spacing:0.2em;color:#d4af37;text-transform:uppercase;">Livraison planifiée</p>
    <p style="margin:0 0 24px;">Bonjour <strong>{{ $order->customer_name }}</strong>,</p>
    <p style="margin:0 0 20px;">Votre commande <strong style="color:#d4af37;">{{ $order->reference }}</strong> pour le modèle <strong>{{ $item?->product_name }}</strong> est prête.</p>
    <p style="margin:0 0 20px;">Notre livreur passera <strong>demain</strong>. Montant COD : <strong style="color:#d4af37;">{{ number_format($order->total, 0, ',', ' ') }} FCFA</strong>.</p>
    <p style="margin:0;font-size:13px;color:#a8a29e;">Un message WhatsApp vous préviendra 30 min avant son arrivée.</p>
@endsection
