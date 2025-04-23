import { BookEntity } from "../../Domain/book.entity";

export interface BookRepositoryPort {
   createBook(params: {
    title: string,
    author: string,
  }): Promise<BookEntity> ;

}
