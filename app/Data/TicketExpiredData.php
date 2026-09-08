<?php

namespace App\Data;

use App\Enums\NotificationType;

final readonly class TicketExpiredData implements NotificationData
{
    public function __construct(
        public string $userName,
        public string $ticketNo,
        public string $eventName,
    ) {
    }

    public function type(): NotificationType
    {
        return NotificationType::TICKET_EXPIRED;
    }
}
