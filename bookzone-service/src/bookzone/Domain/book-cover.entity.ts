import {
  Column,
  Entity,
  JoinColumn,
  OneToOne,
  PrimaryGeneratedColumn,
} from 'typeorm';
import { BookEntity } from './book.entity';

@Entity('book_cover')
export class BookCoverEntity {
  @PrimaryGeneratedColumn('uuid')
  id: string;

  @OneToOne(() => BookEntity)
  @JoinColumn()
  book: BookEntity;

  @Column()
  url: string;

  @Column()
  objectKey: string;
}
