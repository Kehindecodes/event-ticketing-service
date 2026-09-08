<?php

namespace App\Data;

use App\Enums\NotificationType;

interface NotificationData
{
    public function type(): NotificationType;
}
