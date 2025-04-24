import { IQueryHandler, QueryHandler } from "@nestjs/cqrs";
import { SearchBooksQuery } from "./SearchBooksQuery";
import { Logger } from "@nestjs/common";
import { OpenLibraryService } from "src/bookzone/Infrastructure/OpenLibrary/OpenLibraryService";
import { BookSearchResultDto } from "./BookSearchResultDto";

@QueryHandler(SearchBooksQuery)
export class SearchBooksQueryHandler implements IQueryHandler<SearchBooksQuery> {

  private readonly logger = new Logger(SearchBooksQueryHandler.name);
  constructor(private readonly openLibraryService: OpenLibraryService) { }

  async execute(query: SearchBooksQuery): Promise<BookSearchResultDto[]> {
    this.logger.log(`Searching books with query: ${query.query}`);
    return await this.openLibraryService.searchBooks(query.query);
  }
}
