import { BookEntity } from '../../Domain/book.entity';

export interface BookRepositoryPort {
  createBook(params: { title: string; author: string }): Promise<BookEntity>;
  findAll(): Promise<BookEntity[]>;
  findById(id: string): Promise<BookEntity | null>;
}
