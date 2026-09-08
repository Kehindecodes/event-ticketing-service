<?php

namespace App\Enums;

enum NotificationType: string
{
    //  Ticket offered, Ticket confirmed, ticket expired, magic link , payment result
    case TICKET_OFFERED = 'ticket_offered';
    case TICKET_CONFIRMED = 'ticket_confirmed';
    case TICKET_EXPIRED = 'ticket_expired';
    case MAGIC_LINK = 'magic_link';
    case PAYMENT_RESULT = 'payment_result';
}
