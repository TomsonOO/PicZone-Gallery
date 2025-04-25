import { InjectRepository } from '@nestjs/typeorm';
import { BookCoverRepositoryPort } from 'src/bookzone/Application/Port/BookCoverRepositoryPort';
import { BookCoverEntity } from 'src/bookzone/Domain/book-cover.entity';
import { BookEntity } from 'src/bookzone/Domain/book.entity';
import { Repository } from 'typeorm';

export class BookCoverRepositoryPostgresAdapter
  implements BookCoverRepositoryPort
{
  constructor(
    @InjectRepository(BookCoverEntity)
    private readonly bookCoverRepository: Repository<BookCoverEntity>,

    @InjectRepository(BookEntity)
    private readonly bookRepository: Repository<BookEntity>,
  ) {}

  async createBookCover(params: {
    bookId: string;
    url: string;
    objectKey: string;
  }): Promise<BookCoverEntity> {
    const { bookId, url, objectKey } = params;

    const book = await this.bookRepository.findOne({ where: { id: bookId } });

    if (!book) {
      throw new Error(`Book with ID ${bookId} not found`);
    }

    const bookCover = this.bookCoverRepository.create({
      book: book,
      url: url,
      objectKey: objectKey,
    });

    return await this.bookCoverRepository.save(bookCover);
  }
}
