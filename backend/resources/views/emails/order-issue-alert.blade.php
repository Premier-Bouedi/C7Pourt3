@extends('emails.layout')

@section('content')
    <p style="margin:0 0 16px;">Bonjour <strong>{{ $order->customer_name }}</strong>,</p>
    <p style="margin:0 0 24px;">Nous vous contactons concernant votre commande <strong>{{ $order->reference }}</strong>.</p>
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#fafaf9;border:1px solid #e7e5e4;margin-bottom:24px;">
        <tr>
            <td style="padding:16px 20px;">
                <p style="margin:0 0 8px;font-size:12px;text-transform:uppercase;letter-spacing:0.15em;color:#78716c;">Motif</p>
                <p style="margin:0;font-size:16px;font-weight:bold;">{{ $reasonLabel }}</p>
                @if($details)
                    <p style="margin:12px 0 0;font-size:14px;color:#57534e;">{{ $details }}</p>
                @endif
            </td>
        </tr>
    </table>
    <p style="margin:0;font-size:13px;color:#57534e;">Notre équipe travaille à résoudre la situation. Répondez-nous sur WhatsApp pour toute question.</p>
@endsection
