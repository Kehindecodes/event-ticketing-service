import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Notification } from './entities/notification.entity.js';
import { NotificationStatus } from './enums/notification-status.enum.js';
import { NotificationType } from './enums/notification-type.enum.js';

export interface CreateNotificationData {
  userId: string;
  notificationType: NotificationType;
  message: string;
}

@Injectable()
export class NotificationService {
  constructor(
    @InjectRepository(Notification)
    private readonly notificationRepository: Repository<Notification>,
  ) {}

  create(data: CreateNotificationData): Promise<Notification> {
    return this.notificationRepository.save(
      this.notificationRepository.create({
        ...data,
        status: NotificationStatus.PENDING,
      }),
    );
  }

  findByUser(userId: string): Promise<Notification[]> {
    return this.notificationRepository.find({
      where: { userId },
      order: { createdAt: 'DESC' },
    });
  }

  async markSent(id: string): Promise<void> {
    await this.notificationRepository.update(id, {
      status: NotificationStatus.SENT,
    });
  }

  async markFailed(id: string): Promise<void> {
    await this.notificationRepository.update(id, {
      status: NotificationStatus.FAILED,
    });
  }
}
