import { BookVisualizationEntity } from 'src/bookzone/Domain/book-visualization.entity';
import { VisualizationType } from 'src/bookzone/Domain/VisualizationType';

export class BookVisualizationDto {
  id: string;
  bookId: string;
  type: VisualizationType;
  description?: string;
  url: string;
  objectKey: string;
  createdAt: Date;
  needsPresignedUrl: boolean;

  static fromEntity(entity: BookVisualizationEntity): BookVisualizationDto {
    const dto = new BookVisualizationDto();
    dto.id = entity.id;
    dto.bookId = entity.book.id;
    dto.type = entity.type;
    dto.description = entity.description as string;
    dto.url = entity.url;
    dto.objectKey = entity.objectKey;
    dto.createdAt = entity.createdAt;
    dto.needsPresignedUrl = entity.url.includes('s3.amazonaws.com');

    return dto;
  }

  static fromEntities(entities: BookVisualizationEntity[]): BookVisualizationDto[] {
    return entities.map((entity) => BookVisualizationDto.fromEntity(entity));
  }
}
