import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { UserModule } from '../user/user.module.js';
import { Notification } from './entities/notification.entity.js';
import { NotificationService } from './notification.service.js';

@Module({
  imports: [TypeOrmModule.forFeature([Notification]), UserModule],
  providers: [NotificationService],
  exports: [NotificationService],
})
export class NotificationModule {}
