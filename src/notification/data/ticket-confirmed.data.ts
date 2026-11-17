import { NotificationType } from '../enums/notification-type.enum.js';
import { formatDateTime } from './format-date-time.js';
import { NotificationData } from './notification-data.interface.js';

export class TicketConfirmedData implements NotificationData {
  constructor(
    public readonly userName: string,
    public readonly ticketNo: string,
    public readonly eventName: string,
    public readonly venue: string,
    public readonly eventStartsAt: Date,
  ) {}

  type(): NotificationType {
    return NotificationType.TICKET_CONFIRMED;
  }

  message(): string {
    return `Hi ${this.userName}, your ticket ${this.ticketNo} for ${this.eventName} is confirmed. See you at ${this.venue} on ${formatDateTime(this.eventStartsAt)}.`;
  }
}
