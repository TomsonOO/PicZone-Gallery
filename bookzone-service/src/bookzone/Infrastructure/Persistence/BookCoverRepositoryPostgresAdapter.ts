import { BookCoverEntity } from './../../Domain/book-cover.entity';
import { Repository } from 'typeorm';
import { InjectRepository } from '@nestjs/typeorm';

export class BookCoverRepositoryPostgresAdapter {
  constructor(
    @InjectRepository(BookCoverEntity)
    private readonly bookCoverRepository: Repository<BookCoverEntity>,
  ) {}


  
}
