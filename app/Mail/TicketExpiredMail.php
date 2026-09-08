<?php

namespace App\Mail;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketExpiredMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Notification $notification, public User $user)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your ticket offer has expired',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.notifications.ticket-expired',
        );
    }
}
