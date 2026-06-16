<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderIssueAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $reasonLabel,
        public ?string $details = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'C7Pourt3 — Information importante · '.$this->order->reference,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.order-issue-alert');
    }
}
