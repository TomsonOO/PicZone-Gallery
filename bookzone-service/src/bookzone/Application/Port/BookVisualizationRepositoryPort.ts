import { BookVisualizationEntity } from "src/bookzone/Domain/book-visualization.entity"
import { VisualizationType } from "src/bookzone/Domain/VisualizationType"

export interface BookVisualizationRepositoryPort {
  save(params: {
    bookId: string;
    type: VisualizationType;
    description?: string;
    url: string;
    objectKey: string;
  }): Promise<BookVisualizationEntity>;

  findByBookId(bookId: string): Promise<BookVisualizationEntity[]>;

  findByBookIdAndType(bookId: string, type: VisualizationType): Promise<BookVisualizationEntity[]>;

  deleteById(id: string): Promise<void>;
}
