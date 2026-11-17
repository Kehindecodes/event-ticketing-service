import { NotificationType } from '../enums/notification-type.enum.js';
import { formatDateTime } from './format-date-time.js';
import { NotificationData } from './notification-data.interface.js';

export class MagicLinkData implements NotificationData {
  constructor(
    public readonly userName: string,
    public readonly link: string,
    public readonly expiresAt: Date,
  ) {}

  type(): NotificationType {
    return NotificationType.MAGIC_LINK;
  }

  message(): string {
    return `Hi ${this.userName}, use this link to sign in: ${this.link}. It expires on ${formatDateTime(this.expiresAt)}.`;
  }
}
