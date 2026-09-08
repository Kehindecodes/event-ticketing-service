<?php

namespace App\Data;

use App\Enums\NotificationType;
use DateTimeInterface;

final readonly class TicketOfferedData implements NotificationData
{
    public function __construct(
        public string $userName,
        public string $ticketNo,
        public string $eventName,
        public DateTimeInterface $offerExpiresAt,
    ) {
    }

    public function type(): NotificationType
    {
        return NotificationType::TICKET_OFFERED;
    }
}
