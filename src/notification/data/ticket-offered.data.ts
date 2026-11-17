import { NotificationType } from '../enums/notification-type.enum.js';
import { formatDateTime } from './format-date-time.js';
import { NotificationData } from './notification-data.interface.js';

export class TicketOfferedData implements NotificationData {
  constructor(
    public readonly userName: string,
    public readonly ticketNo: string,
    public readonly eventName: string,
    public readonly offerExpiresAt: Date,
  ) {}

  type(): NotificationType {
    return NotificationType.TICKET_OFFERED;
  }

  message(): string {
    return `Hi ${this.userName}, ticket ${this.ticketNo} for ${this.eventName} is available for you. The offer expires on ${formatDateTime(this.offerExpiresAt)}.`;
  }
}
