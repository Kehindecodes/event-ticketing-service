import { Test } from '@nestjs/testing';
import { getRepositoryToken } from '@nestjs/typeorm';
import { Notification } from './entities/notification.entity.js';
import { NotificationStatus } from './enums/notification-status.enum.js';
import { NotificationType } from './enums/notification-type.enum.js';
import { NotificationService } from './notification.service.js';

describe('NotificationService', () => {
  let service: NotificationService;
  const repository = {
    create: vi.fn(),
    save: vi.fn(),
    find: vi.fn(),
    update: vi.fn(),
  };

  beforeEach(async () => {
    vi.resetAllMocks();
    const module = await Test.createTestingModule({
      providers: [
        NotificationService,
        { provide: getRepositoryToken(Notification), useValue: repository },
      ],
    }).compile();
    service = module.get(NotificationService);
  });

  it('creates a notification with pending status', async () => {
    const data = {
      userId: 'u1',
      notificationType: NotificationType.MAGIC_LINK,
      message: 'Your sign-in link',
    };
    const entity = { ...data, status: NotificationStatus.PENDING };
    const saved = { id: 'n1', ...entity };
    repository.create.mockReturnValue(entity);
    repository.save.mockResolvedValue(saved);

    await expect(service.create(data)).resolves.toBe(saved);
    expect(repository.create).toHaveBeenCalledWith(entity);
    expect(repository.save).toHaveBeenCalledWith(entity);
  });

  it('finds notifications by user, newest first', async () => {
    repository.find.mockResolvedValue([]);

    await service.findByUser('u1');
    expect(repository.find).toHaveBeenCalledWith({
      where: { userId: 'u1' },
      order: { createdAt: 'DESC' },
    });
  });

  it('marks a notification as sent', async () => {
    await service.markSent('n1');
    expect(repository.update).toHaveBeenCalledWith('n1', {
      status: NotificationStatus.SENT,
    });
  });

  it('marks a notification as failed', async () => {
    await service.markFailed('n1');
    expect(repository.update).toHaveBeenCalledWith('n1', {
      status: NotificationStatus.FAILED,
    });
  });
});
