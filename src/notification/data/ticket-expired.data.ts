import { NotificationType } from '../enums/notification-type.enum.js';
import { NotificationData } from './notification-data.interface.js';

export class TicketExpiredData implements NotificationData {
  constructor(
    public readonly userName: string,
    public readonly ticketNo: string,
    public readonly eventName: string,
  ) {}

  type(): NotificationType {
    return NotificationType.TICKET_EXPIRED;
  }

  message(): string {
    return `Hi ${this.userName}, your ticket ${this.ticketNo} for ${this.eventName} has expired.`;
  }
}
