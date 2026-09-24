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
use App\Services\DeadLetterPublisher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Exception\RfcComplianceException;

class SendNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Notification $notification) {}

    public int $tries = 3;

    public array $backoff = [10, 60, 300];

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = $this->notification->user;

        if (!$user) {
            Log::error("Cannot send notification: user not found", [
                "notification_id" => $this->notification->id,
            ]);
            $this->notification->update([
                "status" => NotificationStatus::FAILED,
            ]);
            $this->fail();
            return;
        }

        $emailMessage = match ($this->notification->notification_type) {
            NotificationType::TICKET_OFFERED => new TicketOfferedMail(
                $this->notification,
                $user,
            ),
            NotificationType::TICKET_CONFIRMED => new TicketConfirmedMail(
                $this->notification,
                $user,
            ),
            NotificationType::TICKET_EXPIRED => new TicketExpiredMail(
                $this->notification,
                $user,
            ),
            NotificationType::MAGIC_LINK => new MagicLinkMail(
                $this->notification,
                $user,
            ),
            NotificationType::PAYMENT_RESULT => new PaymentResultMail(
                $this->notification,
                $user,
            ),
        };

        try {
            Mail::to($user->email)->send($emailMessage);
            $this->notification->update(["status" => NotificationStatus::SENT]);
        } catch (RfcComplianceException $e) {
            Log::error("Notification email address is invalid, not retrying", [
                "notification_id" => $this->notification->id,
                "error" => $e->getMessage(),
            ]);
            $this->notification->update([
                "status" => NotificationStatus::FAILED,
            ]);
            // fail without retry
            $this->fail($e);
        } catch (\Throwable $e) {
            Log::error("Failed to send notification email", [
                "notification_id" => $this->notification->id,
                "error" => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle a failed job after all retries.
     */
    public function failed(\Throwable $e): void
    {
        Log::error("Notification failed after retries", [
            "notification_id" => $this->notification->id,
            "error" => $e->getMessage(),
        ]);

        $this->notification->update([
            "status" => NotificationStatus::FAILED,
        ]);

        try {
            app(DeadLetterPublisher::class)->publish([
                "notification_id" => $this->notification->id,
                "notification_type" => $this->notification->notification_type,
                "error" => $e->getMessage(),
                "attempts" => $this->attempts(),
            ]);
        } catch (\Throwable $publishError) {
            Log::error("Failed to publish notification to dead letter queue", [
                "notification_id" => $this->notification->id,
                "error" => $publishError->getMessage(),
            ]);
        }
    }
}
