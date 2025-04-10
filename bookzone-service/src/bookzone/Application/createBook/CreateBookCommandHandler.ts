import { CreateBookCommand } from './CreateBookCommand';
import { CommandHandler, ICommandHandler } from '@nestjs/cqrs';
import { Inject } from '@nestjs/common';
import { BookRepositoryPostgresAdapter } from 'src/bookzone/Infrastructure/Persistence/BookRepositoryPostgresAdapter';

@CommandHandler(CreateBookCommand)
export class CreateBookCommandHandler
  implements ICommandHandler<CreateBookCommand> {
  constructor(
    @Inject('BookRepositoryPostgresAdapter')
    private readonly bookRepository: BookRepositoryPostgresAdapter,
  ) { }

  async execute(command: CreateBookCommand) {

    await this.bookRepository.createBook(command);

    return 'Goooowno';
  }
}
