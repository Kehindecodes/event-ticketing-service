import { Test } from '@nestjs/testing';
import { getRepositoryToken } from '@nestjs/typeorm';
import { TicketExpiredData } from './data/ticket-expired.data.js';
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

  it('sends a pending notification built from the data object', async () => {
    const data = new TicketExpiredData('Ada', 'T-1', 'Rock Fest');
    const entity = {
      userId: 'u1',
      notificationType: NotificationType.TICKET_EXPIRED,
      message: data.message(),
      status: NotificationStatus.PENDING,
    };
    const saved = { id: 'n1', ...entity };
    repository.create.mockReturnValue(entity);
    repository.save.mockResolvedValue(saved);

    await expect(service.send('u1', data)).resolves.toBe(saved);
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
