<?php

namespace Tests\Unit\Services;

use App\Data\MagicLinkData;
use App\Enums\NotificationStatus;
use App\Enums\NotificationType;
use App\Jobs\SendNotification;
use App\Mail\MagicLinkMail;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_creates_a_pending_notification_and_returns_without_blocking(): void
    {
        Queue::fake();
        Mail::fake();

        $user = User::factory()->create(["id" => Uuid::uuid4()]);

        $data = new MagicLinkData(
            userName: $user->name,
            link: "https://example.com/magic-link",
            expiresInMinutes: 15,
        );

        app(NotificationService::class)->send($data, $user->id);

        $notification = Notification::where("user_id", $user->id)->sole();

        $this->assertSame(NotificationStatus::PENDING, $notification->status);
        $this->assertSame(
            NotificationType::MAGIC_LINK,
            $notification->notification_type,
        );

        // The job is only recorded, never executed, so send() cannot have
        // blocked on it — proving the dispatch is asynchronous.
        Queue::assertPushedOn(
            "notification",
            SendNotification::class,
            fn(SendNotification $job): bool => (string) $job->notification
                ->id === (string) $notification->id,
        );

        Mail::assertNothingSent();
    }

    public function test_send_delivers_email_to_user_and_changes_notification_status_to_sent(): void
    {
        Queue::fake();
        Mail::fake();

        $user = User::factory()->create(["id" => Uuid::uuid4()]);

        $data = new MagicLinkData(
            userName: $user->name,
            link: "https://example.com/magic-link",
            expiresInMinutes: 15,
        );

        $notification = app(NotificationService::class)->send($data, $user->id);

        $this->assertSame(NotificationStatus::PENDING, $notification->status);

        // Queue::fake() only records the job, so run the job for this
        // notification the way a worker would.
        Queue::pushed(
            SendNotification::class,
            fn(SendNotification $job): bool => (string) $job->notification
                ->id === (string) $notification->id,
        )
            ->sole()
            ->handle();

        Mail::assertSent(
            MagicLinkMail::class,
            fn(MagicLinkMail $mail): bool => $mail->hasTo($user->email),
        );

        $this->assertSame(
            NotificationStatus::SENT,
            $notification->fresh()->status,
        );
    }
}
