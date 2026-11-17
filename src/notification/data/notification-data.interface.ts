import { NotificationType } from '../enums/notification-type.enum.js';

export interface NotificationData {
  type(): NotificationType;
  message(): string;
}
