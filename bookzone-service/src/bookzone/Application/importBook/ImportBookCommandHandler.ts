import { CommandHandler, ICommandHandler } from '@nestjs/cqrs';
import { ImportBookCommand } from './ImportBookCommand';
import { Logger } from '@nestjs/common';
import { OpenLibraryService } from 'src/bookzone/Infrastructure/OpenLibrary/OpenLibraryService';
import { InjectRepository } from '@nestjs/typeorm';
import { BookEntity } from 'src/bookzone/Domain/book.entity';
import { Repository } from 'typeorm';
import { BookCoverEntity } from 'src/bookzone/Domain/book-cover.entity';
import { S3StorageService } from 'src/bookzone/Infrastructure/storage/S3StorageService';
import { v4 as uuidv4 } from 'uuid';

@CommandHandler(ImportBookCommand)
export class ImportBookCommandHandler
  implements ICommandHandler<ImportBookCommand>
{
  private readonly logger = new Logger(ImportBookCommandHandler.name);

  constructor(
    private readonly openLibraryService: OpenLibraryService,
    private readonly s3StorageService: S3StorageService,

    @InjectRepository(BookEntity)
    private readonly bookRepository: Repository<BookEntity>,

    @InjectRepository(BookCoverEntity)
    private readonly bookCoverRepository: Repository<BookCoverEntity>,
  ) {}

  async execute(command: ImportBookCommand): Promise<string> {
    try {
      const bookDetails = await this.openLibraryService.getBookDetailsByKey(
        command.openLibraryKey,
      );

      const title = bookDetails.title;
      const author = bookDetails.authors?.[0]?.name || 'Unknown';

      let coverUrl: string | null = null;
      if (bookDetails.covers && bookDetails.covers.length > 0) {
        coverUrl = `https://covers.openlibrary.org/b/id/${bookDetails.covers[0]}-L.jpg`;
      }

      const book = new BookEntity();
      book.title = title;
      book.author = author;
      book.openLibraryKey = command.openLibraryKey;

      const savedBook = await this.bookRepository.save(book);

      if (coverUrl) {
        const imageBuffer =
          await this.openLibraryService.downloadImage(coverUrl);

        const filename = `${uuidv4()}.jpg`;

        const s3Url = await this.s3StorageService.uploadFile(
          imageBuffer,
          filename,
          'image/jpeg',
          'BookCovers',
        );

        const bookCover = new BookCoverEntity();
        bookCover.book = savedBook;
        bookCover.url = s3Url;
        bookCover.objectKey = filename;

        await this.bookCoverRepository.save(bookCover);
      }

      return savedBook.id;
    } catch (error) {
      this.logger.error(`Failed to import book: ${error.message}`);
      throw new Error('Failed to import book from OpenLibrary');
    }
  }
}
