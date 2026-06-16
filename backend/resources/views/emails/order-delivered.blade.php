@extends('emails.layout')

@section('content')
    <p style="margin:0 0 16px;">Bonjour <strong>{{ $order->customer_name }}</strong>,</p>
    <p style="margin:0 0 24px;">Bonne nouvelle : votre commande <strong>{{ $order->reference }}</strong> a été livrée au Gabon.</p>
    <p style="margin:0 0 24px;">Nous espérons que vos sacs C7Pourt3 vous plaisent. Votre avis compte beaucoup pour nous.</p>
    <p style="margin:0;">
        <a href="{{ url('/avis?ref='.$order->reference) }}" style="display:inline-block;background:#1c1917;color:#ffffff;padding:14px 28px;text-decoration:none;font-size:12px;letter-spacing:0.15em;text-transform:uppercase;">Laisser un avis — Achat vérifié</a>
    </p>
@endsection
