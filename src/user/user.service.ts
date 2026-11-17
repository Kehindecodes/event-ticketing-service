import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { User } from './entities/user.entity.js';
import { UserRole } from './enums/user-role.enum.js';

export interface CreateUserData {
  email: string;
  name: string;
  role?: UserRole;
}

@Injectable()
export class UserService {
  constructor(
    @InjectRepository(User)
    private readonly userRepository: Repository<User>,
  ) {}

  create(data: CreateUserData): Promise<User> {
    return this.userRepository.save(this.userRepository.create(data));
  }

  findById(id: string): Promise<User | null> {
    return this.userRepository.findOneBy({ id });
  }

  findByEmail(email: string): Promise<User | null> {
    return this.userRepository.findOneBy({ email });
  }
}
