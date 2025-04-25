import { IQueryHandler, QueryHandler } from '@nestjs/cqrs';
import { GetBooksQuery } from './GetBooksQuery';
import { Inject } from '@nestjs/common';
import { BookRepositoryPort } from '../Port/BookRepositoryPort';
import { BookDto } from './BookDto';

@QueryHandler(GetBooksQuery)
export class GetBooksQueryHandler implements IQueryHandler<GetBooksQuery> {
  constructor(
    @Inject('BookRepositoryPostgresAdapter')
    private readonly bookRepository: BookRepositoryPort,
  ) {}

  async execute(query: GetBooksQuery): Promise<BookDto[]> {
    const books = await this.bookRepository.findAll();
    return BookDto.fromEntities(books);
  }
}
