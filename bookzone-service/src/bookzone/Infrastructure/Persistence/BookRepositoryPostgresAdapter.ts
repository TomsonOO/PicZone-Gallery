import { BookEntity } from './../../Domain/book.entity';
import { Repository } from 'typeorm';
import { InjectRepository } from '@nestjs/typeorm';

export class BookRepositoryPostgresAdapter {
  constructor(
    @InjectRepository(BookEntity)
    private readonly bookRepository: Repository<BookEntity>,
  ) {}

  async createBook(params: {
    title: string,
    author: string,
  }
  ): Promise<void> {
    const { title, author } = params;

    let book = this.bookRepository.create({
      title: title,
      author: author,
      createdAt: new Date(),
    });

    await this.bookRepository.save(book);
  }
}
