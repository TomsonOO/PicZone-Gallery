import { IQueryHandler, QueryHandler } from '@nestjs/cqrs';
import { GetBooksQuery } from '../getBooks/GetBooksQuery';
import { getBookDetailsQuery } from './GetBookDetailsQuery';
import { Inject, NotFoundException } from '@nestjs/common';
import { BookRepositoryPort } from '../Port/BookRepositoryPort';
import { BookDto } from '../getBooks/BookDto';

@QueryHandler(getBookDetailsQuery)
export class GetBookDetailsQueryHandler
  implements IQueryHandler<GetBooksQuery>
{
  constructor(
    @Inject('BookRepositoryPostgresAdapter')
    private readonly bookRepository: BookRepositoryPort,
  ) {}

  async execute(query: getBookDetailsQuery): Promise<BookDto> {
    const book = await this.bookRepository.findById(query.bookId);
    if (!book) {
      throw new NotFoundException(`Book with ID ${query.bookId} not found`);
    }
    return BookDto.fromEntity(book);
  }
}
