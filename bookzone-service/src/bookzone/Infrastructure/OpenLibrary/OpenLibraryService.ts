import { Injectable, Logger } from "@nestjs/common";
import axios from "axios";
import { BookSearchResultDto } from "src/bookzone/Application/searchBooks/BookSearchResultDto";

@Injectable()
export class OpenLibraryService {

  private readonly logger = new Logger(OpenLibraryService.name);
  private readonly baseUrl = 'https://openlibrary.org/search.json';


  async searchBooks(query: string): Promise<BookSearchResultDto[]> {

    try {
      const response = await axios.get(this.baseUrl, {
        params: {
          q: query,
          limit: 10,
        },
      });

      if (response.data && response.data.docs) {
        return response.data.docs.map(book => ({
          title: book.title,
          author: book.author_name ? book.author_name[0] : 'Unknown',
          coverUrl: book.cover_i ? `https://covers.openlibrary.org/b/id/${book.cover_i}-M.jpg` : null,
        }));
      }

      return [];
    } catch (error) {
      this.logger.error(`Error searching OpenLibrary: ${error.message}`);
      throw new Error('Failed to search books from OpenLibrary');
    }
  }
}
