import { Column, CreateDateColumn, Entity, JoinColumn, ManyToOne, PrimaryGeneratedColumn } from "typeorm";
import { BookEntity } from "./book.entity";
import { VisualizationType } from "./VisualizationType";

@Entity('book_visualization')
export class BookVisualizationEntity {

  @PrimaryGeneratedColumn('uuid')
  id: string;

  @ManyToOne(() => BookEntity, { onDelete: 'CASCADE' })
  @JoinColumn()
  book: BookEntity;

  @Column()
  type: VisualizationType;

  @Column({ type: 'text', nullable: true })
  description: string;

  @Column()
  url: string;

  @Column()
  objectKey: string;

  @CreateDateColumn({ name: 'created_at' })
  createdAt: Date;
}
