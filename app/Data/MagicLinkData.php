<?php

namespace App\Data;

use App\Enums\NotificationType;

final readonly class MagicLinkData implements NotificationData
{
    public function __construct(
        public string $userName,
        public string $link,
        public int $expiresInMinutes,
    ) {
    }

    public function type(): NotificationType
    {
        return NotificationType::MAGIC_LINK;
    }
}
