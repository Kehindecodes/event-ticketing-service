import { NotificationType } from '../enums/notification-type.enum.js';
import { PaymentStatus } from '../enums/payment-status.enum.js';
import { formatDateTime } from './format-date-time.js';
import { NotificationData } from './notification-data.interface.js';

export class PaymentResultData implements NotificationData {
  constructor(
    public readonly userName: string,
    public readonly status: PaymentStatus,
    public readonly paymentCode: string,
    public readonly amount: string,
    public readonly ticketNo: string,
    public readonly eventName: string,
    public readonly paidAt: Date,
  ) {}

  type(): NotificationType {
    return NotificationType.PAYMENT_RESULT;
  }

  message(): string {
    const outcome =
      this.status === PaymentStatus.SUCCESS ? 'was successful' : 'failed';

    return `Hi ${this.userName}, your payment ${this.paymentCode} of ${this.amount} for ticket ${this.ticketNo} (${this.eventName}) ${outcome} on ${formatDateTime(this.paidAt)}.`;
  }
}
