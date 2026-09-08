<?php

namespace App\Services;

use App\Enums\NotificationStatus;
use App\Enums\NotificationType;
use App\Jobs\SendNotification;
use App\Models\Notification;
use Ramsey\Uuid\Uuid;

class NotificationService
{
    public function send(NotificationType $type, string $message, string $userId): void
    {
        try {
            $notification = new Notification([
                'id' => Uuid::uuid4(),
                'notification_type' => $type,
                'message' => $message,
                'user_id' => $userId,
                'status' => NotificationStatus::PENDING,
            ]);

            $notification->save();

            SendNotification::dispatch($notification)->onQueue('notification');
        } catch (\Exception $e) {
            throw new \Exception('Failed to send notification: ' . $e->getMessage());
        }
    }
}
