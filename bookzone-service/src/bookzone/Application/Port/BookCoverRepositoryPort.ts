import { BookCoverEntity } from 'src/bookzone/Domain/book-cover.entity';

export interface BookCoverRepositoryPort {
  createBookCover(params: {
    bookId: string;
    url: string;
    objectKey: string;
  }): Promise<BookCoverEntity>;
}
