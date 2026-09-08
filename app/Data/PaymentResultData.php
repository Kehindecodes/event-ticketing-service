<?php

namespace App\Data;

use App\Enums\NotificationType;
use App\Enums\PaymentStatus;
use DateTimeInterface;

final readonly class PaymentResultData implements NotificationData
{
    public function __construct(
        public string $userName,
        public PaymentStatus $status,
        public string $paymentCode,
        public string $amount,
        public string $ticketNo,
        public string $eventName,
        public DateTimeInterface $paidAt,
    ) {
    }

    public function type(): NotificationType
    {
        return NotificationType::PAYMENT_RESULT;
    }
}
