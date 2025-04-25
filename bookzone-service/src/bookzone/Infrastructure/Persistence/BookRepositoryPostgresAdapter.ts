import { BookEntity } from './../../Domain/book.entity';
import { Repository } from 'typeorm';
import { InjectRepository } from '@nestjs/typeorm';
import { BookRepositoryPort } from 'src/bookzone/Application/Port/BookRepositoryPort';

export class BookRepositoryPostgresAdapter implements BookRepositoryPort {
  constructor(
    @InjectRepository(BookEntity)
    private readonly bookRepository: Repository<BookEntity>,
  ) {}

  async createBook(params: {
    title: string;
    author: string;
  }): Promise<BookEntity> {
    const { title, author } = params;

    let book = this.bookRepository.create({
      title: title,
      author: author,
    });

    return await this.bookRepository.save(book);
  }

  async findAll(): Promise<BookEntity[]> {
    return await this.bookRepository.find({
      relations: ['cover']
    });
  }
}
