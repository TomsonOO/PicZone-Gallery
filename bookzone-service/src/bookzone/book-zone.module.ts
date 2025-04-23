import { Module } from '@nestjs/common';
import { ConfigModule } from '@nestjs/config';
import { TypeOrmModule } from '@nestjs/typeorm';
import { WebBookZoneAdapter } from './Infrastructure/Web/WebBookZoneAdapter';
import { CqrsModule } from '@nestjs/cqrs';
import { databaseConfig } from './../../config/database.config';
import { BookRepositoryPostgresAdapter } from './Infrastructure/Persistence/BookRepositoryPostgresAdapter';
import { BookCoverRepositoryPostgresAdapter } from './Infrastructure/Persistence/BookCoverRepositoryPostgresAdapter';
import { BookEntity } from './Domain/book.entity';
import { BookCoverEntity } from './Domain/book-cover.entity';
import { CreateBookCommandHandler } from './Application/createBook/CreateBookCommandHandler';

const CommandHandlers = [ 
  CreateBookCommandHandler,
];

const RepositoryProviders = [
  {
      provide: 'BookRepositoryPostgresAdapter',
      useClass: BookRepositoryPostgresAdapter,
    },
    {
      provide: 'BookCoverRepositoryPostgresAdapter',
      useClass: BookCoverRepositoryPostgresAdapter,
    },
];

@Module({
  imports: [
    ConfigModule.forRoot({
      isGlobal: true,
    }),
    TypeOrmModule.forRoot(databaseConfig),
    TypeOrmModule.forFeature([
      BookEntity,
      BookCoverEntity,
    ]),
    CqrsModule],
    providers: [
    ...RepositoryProviders,
    ...CommandHandlers,
  ],
  controllers: [WebBookZoneAdapter],
})
export class BookZoneModule { }
