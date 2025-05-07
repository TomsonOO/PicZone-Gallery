import { InjectRepository } from "@nestjs/typeorm";
import { BookVisualizationRepositoryPort } from "src/bookzone/Application/Port/BookVisualizationRepositoryPort";
import { BookVisualizationEntity } from "src/bookzone/Domain/book-visualization.entity";
import { VisualizationType } from "src/bookzone/Domain/VisualizationType";
import { BookEntity } from "src/bookzone/Domain/book.entity";
import { Repository } from "typeorm";



export class BookVisualizationRepositoryPostgresAdapter implements BookVisualizationRepositoryPort {

  constructor(
    @InjectRepository(BookVisualizationEntity)
    private readonly bookVisualizationRepository: Repository<BookVisualizationEntity>,

    @InjectRepository(BookEntity)
    private readonly bookRepository: Repository<BookEntity>,
  ) { }

  async save(params: { bookId: string; type: VisualizationType; description?: string; url: string; objectKey: string; }): Promise<BookVisualizationEntity> {
    const { bookId, type, description, url, objectKey } = params;
    const book = await this.bookRepository.findOne({ where: { id: bookId } });

    if (!book) {
      throw new Error(`Book with ID ${bookId} not found`);
    }

    const visualization = new BookVisualizationEntity();
    visualization.book = book;
    visualization.type = type;
    visualization.description = description || '';
    visualization.url = url;
    visualization.objectKey = objectKey;

    return await this.bookVisualizationRepository.save(visualization);
  }

  async findByBookId(bookId: string): Promise<BookVisualizationEntity[]> {
    return await this.bookVisualizationRepository.find({
      where: {
        book: {
          id: bookId
        }
      },
      order: {
        createdAt: 'DESC',
      },
    });
  }

  async findByBookIdAndType(bookId: string, type: VisualizationType): Promise<BookVisualizationEntity[]> {
    return await this.bookVisualizationRepository.find({
      where: {
        book: { id: bookId },
        type: type,
      },
      order: {
        createdAt: 'DESC',
      }
    });
  }

  async deleteById(id: string): Promise<void> {
    const result = await this.bookVisualizationRepository.delete(id);

    if (result.affected === 0) {
      throw new Error(`Visualization with ID ${id} not found`);
    }
  }
}
