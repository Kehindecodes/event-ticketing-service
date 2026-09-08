<?php

namespace App\Services;

use App\Data\MagicLinkData;
use App\Data\NotificationData;
use App\Data\PaymentResultData;
use App\Data\TicketConfirmedData;
use App\Data\TicketExpiredData;
use App\Data\TicketOfferedData;
use App\Enums\NotificationStatus;
use App\Enums\PaymentStatus;
use App\Jobs\SendNotification;
use App\Models\Notification;
use Ramsey\Uuid\Uuid;

class NotificationService
{
    public function send(NotificationData $data, string $userId): void
    {
        try {
            $notification = new Notification([
                'id' => Uuid::uuid4(),
                'notification_type' => $data->type(),
                'message' => $this->getMessage($data),
                'user_id' => $userId,
                'status' => NotificationStatus::PENDING,
            ]);

            $notification->save();

            SendNotification::dispatch($notification)->onQueue('notification');
        } catch (\Exception $e) {
            throw new \Exception('Failed to send notification: ' . $e->getMessage());
        }
    }

    private function getMessage(NotificationData $data): string
    {
        return match (true) {
            $data instanceof MagicLinkData => $this->magicLinkMessage($data),
            $data instanceof TicketOfferedData => $this->ticketOfferedMessage($data),
            $data instanceof TicketConfirmedData => $this->ticketConfirmedMessage($data),
            $data instanceof TicketExpiredData => $this->ticketExpiredMessage($data),
            $data instanceof PaymentResultData => $this->paymentResultMessage($data),
        };
    }

    private function magicLinkMessage(MagicLinkData $data): string
    {
        return "Hi {$data->userName}, use the link below to sign in to your account. "
            ."This link expires in {$data->expiresInMinutes} minutes.\n\n{$data->link}";
    }

    private function ticketOfferedMessage(TicketOfferedData $data): string
    {
        return "Hi {$data->userName}, a ticket ({$data->ticketNo}) for {$data->eventName} "
            ."has been offered to you. Confirm before {$data->offerExpiresAt->format('D, d M Y H:i')} to secure it.";
    }

    private function ticketConfirmedMessage(TicketConfirmedData $data): string
    {
        return "Hi {$data->userName}, your ticket ({$data->ticketNo}) for {$data->eventName} "
            ."at {$data->venue} is confirmed for {$data->eventStartsAt->format('D, d M Y H:i')}.";
    }

    private function ticketExpiredMessage(TicketExpiredData $data): string
    {
        return "Hi {$data->userName}, your ticket offer ({$data->ticketNo}) for {$data->eventName} has expired.";
    }

    private function paymentResultMessage(PaymentResultData $data): string
    {
        return $data->status === PaymentStatus::SUCCESSFUL
            ? "Hi {$data->userName}, your payment ({$data->paymentCode}) of {$data->amount} for ticket {$data->ticketNo} "
                ."({$data->eventName}) on {$data->paidAt->format('D, d M Y H:i')} was successful."
            : "Hi {$data->userName}, your payment ({$data->paymentCode}) of {$data->amount} for ticket {$data->ticketNo} "
                ."({$data->eventName}) failed. Please try again.";
    }
}
