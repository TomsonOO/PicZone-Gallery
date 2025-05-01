import { Module } from '@nestjs/common';
import { ConfigModule } from '@nestjs/config';
import { TypeOrmModule } from '@nestjs/typeorm';
import { WebBookZoneAdapter } from './Infrastructure/Web/WebBookZoneAdapter';
import { CqrsModule } from '@nestjs/cqrs';
import { HttpModule } from '@nestjs/axios';
import { databaseConfig } from './../../config/database.config';
import { BookRepositoryPostgresAdapter } from './Infrastructure/Persistence/BookRepositoryPostgresAdapter';
import { BookCoverRepositoryPostgresAdapter } from './Infrastructure/Persistence/BookCoverRepositoryPostgresAdapter';
import { BookEntity } from './Domain/book.entity';
import { BookCoverEntity } from './Domain/book-cover.entity';
import { CreateBookCommandHandler } from './Application/createBook/CreateBookCommandHandler';
import { GetBooksQueryHandler } from './Application/getBooks/GetBooksQueryHandler';
import { S3StorageService } from './Infrastructure/storage/S3StorageService';
import { SearchBooksQueryHandler } from './Application/searchBooks/SearchBooksQueryHandler';
import { OpenLibraryService } from './Infrastructure/OpenLibrary/OpenLibraryService';
import { ImportBookCommandHandler } from './Application/importBook/ImportBookCommandHandler';
import { GetBookCoverPresignedUrlQueryHandler } from './Application/getBookCoverPresignedUrl/GetBookCoverPresignedUrlQueryHandler';
import { IdeogramImageGeneratorAdapter } from './Infrastructure/ImageGeneration/IdeogramImageGeneratorAdapter';
import { GenerateImageHandler } from './Application/generateImage/GenerateImageCommandHandler';

const CommandHandlers = [CreateBookCommandHandler, ImportBookCommandHandler];

const QueryHandlers = [
  GetBooksQueryHandler,
  SearchBooksQueryHandler,
  GetBookCoverPresignedUrlQueryHandler,
  GenerateImageHandler,
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
  {
    provide: 'S3StorageService',
    useClass: S3StorageService,
  },
];

const Services = [OpenLibraryService];

const ImageGenerationProvider = {
  provide: 'ImageGenerationPort',
  useClass: IdeogramImageGeneratorAdapter,
};

@Module({
  imports: [
    ConfigModule.forRoot({
      isGlobal: true,
    }),
    TypeOrmModule.forRoot(databaseConfig),
    TypeOrmModule.forFeature([BookEntity, BookCoverEntity]),
    CqrsModule,
    HttpModule,
  ],
  providers: [
    ...RepositoryProviders,
    ...CommandHandlers,
    ...QueryHandlers,
    ...Services,
    ImageGenerationProvider,
  ],
  controllers: [WebBookZoneAdapter],
})
export class BookZoneModule {}
