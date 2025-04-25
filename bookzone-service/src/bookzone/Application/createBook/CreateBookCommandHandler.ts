import { CreateBookCommand } from './CreateBookCommand';
import { CommandHandler, ICommandHandler } from '@nestjs/cqrs';
import { Inject, Logger } from '@nestjs/common';
import { BookRepositoryPort } from '../Port/BookRepositoryPort';
import { BookCoverRepositoryPort } from '../Port/BookCoverRepositoryPort';

@CommandHandler(CreateBookCommand)
export class CreateBookCommandHandler
  implements ICommandHandler<CreateBookCommand>
{
  private readonly logger = new Logger(CreateBookCommandHandler.name);
  constructor(
    @Inject('BookCoverRepositoryPostgresAdapter')
    private readonly bookCoverRepository: BookCoverRepositoryPort,

    @Inject('BookRepositoryPostgresAdapter')
    private readonly bookRepository: BookRepositoryPort,
  ) {}

  async execute(command: CreateBookCommand): Promise<string> {
    this.logger.log(`Creating book with title ${command.title}`);

    try {
      const book = await this.bookRepository.createBook({
        title: command.title,
        author: command.author,
      });

      if (command.url && command.objectKey) {
        await this.bookCoverRepository.createBookCover({
          bookId: book.id,
          url: command.url,
          objectKey: command.objectKey,
        });
      }

      this.logger.log(`Book created with ID: ${book.id}`);
      return book.id;
    } catch (error) {
      this.logger.error(`Failed to create book: ${error.message}`, error.stack);
      throw error;
    }
  }
}
