import { CreateBookCommand } from './CreateBookCommand';
import { CommandHandler, ICommandHandler } from '@nestjs/cqrs';
import { Inject, Logger } from '@nestjs/common';
import { BookRepositoryPostgresAdapter } from 'src/bookzone/Infrastructure/Persistence/BookRepositoryPostgresAdapter';
import { BookRepositoryPort } from '../Port/BookRepositoryPort';

@CommandHandler(CreateBookCommand)
export class CreateBookCommandHandler
  implements ICommandHandler<CreateBookCommand> {

  private readonly logger = new Logger(CreateBookCommandHandler.name);
  constructor(
    @Inject('BookRepositoryPostgresAdapter')
    private readonly bookRepository: BookRepositoryPort,
  ) { }

  async execute(command: CreateBookCommand): Promise<void> {

    this.logger.log(`Creating book with title ${command.title}`);

    try {
      const book = await this.bookRepository.createBook({
        title: command.title,
        author: command.author
      });
    this.logger.log(`Book created with ID: ${book.id}`);
    }
    catch (error) {
      this.logger.error(`Failed to create book: ${error.message}`, error.stack);
      throw error;
    }
  }

}
