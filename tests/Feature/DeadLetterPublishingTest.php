<?php

namespace Tests\Feature;

use App\Enums\NotificationStatus;
use App\Enums\NotificationType;
use App\Jobs\SendNotification;
use App\Models\Notification;
use App\Models\User;
use App\Services\DeadLetterPublisher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class DeadLetterPublishingTest extends TestCase
{
    use RefreshDatabase;

    private function makeNotification(): Notification
    {
        $user = User::factory()->create(['id' => (string) Uuid::uuid4()]);

        return Notification::create([
            'id' => (string) Uuid::uuid4(),
            'user_id' => $user->id,
            'notification_type' => NotificationType::MAGIC_LINK,
            'message' => 'Hi, use this link to sign in.',
            'status' => NotificationStatus::PENDING,
        ]);
    }

    public function test_failed_publishes_the_job_to_the_dead_letter_exchange(): void
    {
        $notification = $this->makeNotification();

        $this->mock(DeadLetterPublisher::class)
            ->shouldReceive('publish')
            ->once()
            ->with(\Mockery::on(fn (array $payload) => $payload['notification_id'] === $notification->id
                && $payload['error'] === 'boom'));

        (new SendNotification($notification))->failed(new \RuntimeException('boom'));

        $this->assertSame(NotificationStatus::FAILED, $notification->fresh()->status);
    }

    public function test_failed_does_not_throw_when_the_broker_is_unreachable(): void
    {
        $notification = $this->makeNotification();

        $this->mock(DeadLetterPublisher::class)
            ->shouldReceive('publish')
            ->andThrow(new \RuntimeException('connection refused'));

        (new SendNotification($notification))->failed(new \RuntimeException('boom'));

        $this->assertSame(NotificationStatus::FAILED, $notification->fresh()->status);
    }
}
