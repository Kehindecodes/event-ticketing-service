<?php

namespace App\Models;

use App\Enums\NotificationStatus;
use App\Enums\NotificationType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(
    'id',
    'user_id',
    'notification_type',
    'status',
    'message',
)]
class Notification extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected function casts(): array
    {
        return [
            'notification_type' => NotificationType::class,
            'status' => NotificationStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
