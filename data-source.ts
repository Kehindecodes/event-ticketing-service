import 'dotenv/config';
import { DataSource } from 'typeorm';
import { User } from './src/user/entities/user.entity.js';
import { Notification } from './src/notification/entities/notification.entity.js';

export default new DataSource({
  type: 'postgres',
  host: process.env.DB_HOST,
  port: Number(process.env.DB_PORT),
  username: process.env.DB_USER,
  password: process.env.DB_PASSWORD,
  database: process.env.DB_NAME,
  entities: [User, Notification],
  migrations: ['src/database/migrations/*.ts'],
});
