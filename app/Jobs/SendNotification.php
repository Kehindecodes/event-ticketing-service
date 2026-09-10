<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\NotificationStatus;
use App\Enums\NotificationType;
use App\Mail\MagicLinkMail;
use App\Mail\PaymentResultMail;
use App\Mail\TicketConfirmedMail;
use App\Mail\TicketExpiredMail;
use App\Mail\TicketOfferedMail;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Notification $notification)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = $this->notification->user;

        if (! $user) {
            Log::error('Cannot send notification: user not found', ['notification_id' => $this->notification->id]);
            $this->notification->update(['status' => NotificationStatus::FAILED]);
            return;
        }

        $emailMessage = match ($this->notification->notification_type) {
            NotificationType::TICKET_OFFERED => new TicketOfferedMail($this->notification, $user),
            NotificationType::TICKET_CONFIRMED => new TicketConfirmedMail($this->notification, $user),
            NotificationType::TICKET_EXPIRED => new TicketExpiredMail($this->notification, $user),
            NotificationType::MAGIC_LINK => new MagicLinkMail($this->notification, $user),
            NotificationType::PAYMENT_RESULT => new PaymentResultMail($this->notification, $user),
        };

        try {
            Mail::to($user->email)->send($emailMessage);

            $this->notification->update(['status' => NotificationStatus::SENT]);
        } catch (\Throwable $e) {
            Log::error('Failed to send notification email', [
                'notification_id' => $this->notification->id,
                'error' => $e->getMessage(),
            ]);

            $this->notification->update(['status' => NotificationStatus::FAILED]);

            throw $e;
        }
    }
}
