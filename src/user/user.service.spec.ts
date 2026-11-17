import { Test } from '@nestjs/testing';
import { getRepositoryToken } from '@nestjs/typeorm';
import { User } from './entities/user.entity.js';
import { UserRole } from './enums/user-role.enum.js';
import { UserService } from './user.service.js';

describe('UserService', () => {
  let service: UserService;
  const repository = {
    create: vi.fn(),
    save: vi.fn(),
    findOneBy: vi.fn(),
  };

  beforeEach(async () => {
    vi.resetAllMocks();
    const module = await Test.createTestingModule({
      providers: [
        UserService,
        { provide: getRepositoryToken(User), useValue: repository },
      ],
    }).compile();
    service = module.get(UserService);
  });

  it('creates and saves a user', async () => {
    const data = { email: 'a@b.com', name: 'Ada', role: UserRole.ORGANIZER };
    const entity = { ...data };
    const saved = { id: 'u1', ...data };
    repository.create.mockReturnValue(entity);
    repository.save.mockResolvedValue(saved);

    await expect(service.create(data)).resolves.toBe(saved);
    expect(repository.create).toHaveBeenCalledWith(data);
    expect(repository.save).toHaveBeenCalledWith(entity);
  });

  it('finds a user by id', async () => {
    const user = { id: 'u1' };
    repository.findOneBy.mockResolvedValue(user);

    await expect(service.findById('u1')).resolves.toBe(user);
    expect(repository.findOneBy).toHaveBeenCalledWith({ id: 'u1' });
  });

  it('finds a user by email', async () => {
    repository.findOneBy.mockResolvedValue(null);

    await expect(service.findByEmail('a@b.com')).resolves.toBeNull();
    expect(repository.findOneBy).toHaveBeenCalledWith({ email: 'a@b.com' });
  });
});
