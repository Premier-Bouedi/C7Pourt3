@extends('emails.layout')

@section('content')
    <p style="margin:0 0 8px;font-size:12px;letter-spacing:0.2em;color:#d4af37;text-transform:uppercase;">Votre avis compte ✨</p>
    <p style="margin:0 0 24px;">Bonjour <strong>{{ $order->customer_name }}</strong>,</p>
    <p style="margin:0 0 20px;">Nous espérons que votre nouveau sac C7Pourt3 vous plaît et fait sensation ! Votre avis compte énormément pour notre jeune marque.</p>
    <p style="margin:0 0 28px;">Prenez une minute pour laisser un commentaire — badge <strong style="color:#d4af37;">Achat vérifié</strong> après validation.</p>
    <p style="margin:0;text-align:center;">
        <a href="{{ url('/avis?ref='.$order->reference) }}" style="display:inline-block;background:#d4af37;color:#1c1917;padding:14px 32px;text-decoration:none;font-size:11px;letter-spacing:0.2em;text-transform:uppercase;font-weight:bold;">Laisser mon avis</a>
    </p>
@endsection
