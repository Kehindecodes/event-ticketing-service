<?php

namespace App\Enums;

enum NotificationStatus: string
{
    case PENDING = 'Pending';
    case SENT = 'Sent';
    case FAILED = 'Failed';

}
