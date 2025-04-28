import { BookEntity } from '../../Domain/book.entity';

export class BookDto {
  id: string;
  title: string;
  author: string;
  createdAt: Date;
  coverUrl?: string;
  objectKey?: string;
  openLibraryKey?: string;

  static fromEntity(entity: BookEntity): BookDto {
    const dto = new BookDto();
    dto.id = entity.id;
    dto.title = entity.title;
    dto.author = entity.author;
    dto.createdAt = entity.createdAt;
    dto.openLibraryKey = entity.openLibraryKey;
    if (entity.cover) {
      dto.coverUrl = entity.cover.url;
      dto.objectKey = entity.cover.objectKey;
    }
    return dto;
  }

  static fromEntities(entities: BookEntity[]): BookDto[] {
    return entities.map((entity) => this.fromEntity(entity));
  }
}
