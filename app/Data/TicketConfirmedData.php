<?php

namespace App\Data;

use App\Enums\NotificationType;
use DateTimeInterface;

final readonly class TicketConfirmedData implements NotificationData
{
    public function __construct(
        public string $userName,
        public string $ticketNo,
        public string $eventName,
        public string $venue,
        public DateTimeInterface $eventStartsAt,
    ) {
    }

    public function type(): NotificationType
    {
        return NotificationType::TICKET_CONFIRMED;
    }
}
