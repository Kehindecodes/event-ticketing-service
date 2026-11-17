import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Notification } from './entities/notification.entity.js';
import { NotificationStatus } from './enums/notification-status.enum.js';
import { NotificationData } from './data/notification-data.interface.js';

@Injectable()
export class NotificationService {
  constructor(
    @InjectRepository(Notification)
    private readonly notificationRepository: Repository<Notification>,
  ) {}

  send(userId: string, data: NotificationData): Promise<Notification> {
    const newNotification = this.notificationRepository.create({
      userId,
      notificationType: data.type(),
      message: data.message(),
      status: NotificationStatus.PENDING,
    });
    const savedNotification = this.notificationRepository.save(newNotification);

    // enqueue notification for processing

    return savedNotification;
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
