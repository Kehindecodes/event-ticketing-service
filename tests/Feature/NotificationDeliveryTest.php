<?php

namespace Tests\Feature;

use App\Data\MagicLinkData;
use App\Enums\NotificationStatus;
use App\Enums\NotificationType;
use App\Jobs\SendNotification;
use App\Mail\MagicLinkMail;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class NotificationDeliveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_lands_a_real_job_on_the_notification_queue(): void
    {
        // Use the real "database" queue backend instead of Queue::fake(), so
        // this proves the job actually serializes and lands in storage that
        // a worker would consume from — not just that dispatch() was called.
        config(['queue.default' => 'database']);

        $user = User::factory()->create(['id' => (string) Uuid::uuid4()]);

        $data = new MagicLinkData(
            userName: $user->name,
            link: 'https://example.com/magic-link',
            expiresInMinutes: 15,
        );

        app(NotificationService::class)->send($data, $user->id);

        $notification = Notification::where('user_id', $user->id)->sole();

        $this->assertSame(NotificationStatus::PENDING, $notification->status);

        $job = DB::table('jobs')->where('queue', 'notification')->sole();

        $payload = json_decode($job->payload, true);

        $this->assertSame(SendNotification::class, $payload['displayName']);
        $this->assertStringContainsString((string) $notification->id, $job->payload);
    }

    public function test_job_marks_notification_failed_when_referenced_user_no_longer_exists(): void
    {
        $user = User::factory()->create(['id' => (string) Uuid::uuid4()]);

        $notification = Notification::create([
            'id' => (string) Uuid::uuid4(),
            'user_id' => $user->id,
            'notification_type' => NotificationType::MAGIC_LINK,
            'message' => 'Hi, use this link to sign in.',
            'status' => NotificationStatus::PENDING,
        ]);

      
        $notification->setRelation('user', null);

        (new SendNotification($notification))->handle();

        $this->assertSame(NotificationStatus::FAILED, $notification->fresh()->status);
    }

    // public function test_job_marks_notification_failed_and_rethrows_when_mail_delivery_fails(): void
    // {
    //     $user = User::factory()->create(['id' => (string) Uuid::uuid4()]);

    //     $notification = Notification::create([
    //         'id' => (string) Uuid::uuid4(),
    //         'user_id' => $user->id,
    //         'notification_type' => NotificationType::MAGIC_LINK,
    //         'message' => 'Hi, use this link to sign in.',
    //         'status' => NotificationStatus::PENDING,
    //     ]);

    //     Mail::shouldReceive('to')
    //         ->once()
    //         ->with($user->email)
    //         ->andReturnSelf();

    //     Mail::shouldReceive('send')
    //         ->once()
    //         ->with(\Mockery::type(MagicLinkMail::class))
    //         ->andThrow(new \RuntimeException('SMTP connection refused'));

    //     try {
    //         (new SendNotification($notification))->handle();
    //         $this->fail('Expected handle() to rethrow the mail delivery failure.');
    //     } catch (\RuntimeException $e) {
    //         $this->assertSame('SMTP connection refused', $e->getMessage());
    //     }

    //     $this->assertSame(NotificationStatus::FAILED, $notification->fresh()->status);
    // }
}
