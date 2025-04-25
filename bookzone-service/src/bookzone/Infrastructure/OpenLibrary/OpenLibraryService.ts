import { Injectable, Logger } from '@nestjs/common';
import axios from 'axios';
import { BookSearchResultDto } from 'src/bookzone/Application/searchBooks/BookSearchResultDto';

@Injectable()
export class OpenLibraryService {
  private readonly logger = new Logger(OpenLibraryService.name);
  private readonly baseUrl = 'https://openlibrary.org/search.json';
  private readonly detailsBaseUrl = 'https://openlibrary.org';
  private readonly coversBaseUrl = 'https://covers.openlibrary.org';

  async searchBooks(query: string): Promise<BookSearchResultDto[]> {
    try {
      const response = await axios.get(this.baseUrl, {
        params: {
          q: query,
          limit: 10,
        },
      });

      if (response.data && response.data.docs) {
        return response.data.docs.map((book) => ({
          title: book.title,
          author: book.author_name ? book.author_name[0] : 'Unknown',
          coverUrl: book.cover_i
            ? `${this.coversBaseUrl}/b/id/${book.cover_i}-M.jpg`
            : null,
          openLibraryKey: book.key || null,
        }));
      }

      return [];
    } catch (error) {
      this.logger.error(`Error searching OpenLibrary: ${error.message}`);
      throw new Error('Failed to search books from OpenLibrary');
    }
  }

  async getBookDetailsByKey(openLibraryKey: string): Promise<any> {
    try {
      const response = await axios.get(
        `${this.detailsBaseUrl}${openLibraryKey}.json`,
      );
      return response.data;
    } catch (error) {
      this.logger.error(`Error fetching book details: ${error.message}`);
      throw new Error('Failed to fetch book details from OpenLibrary');
    }
  }

  async downloadImage(imageUrl: string): Promise<Buffer> {
    try {
      const response = await axios.get(imageUrl, {
        responseType: 'arraybuffer',
      });
      return Buffer.from(response.data);
    } catch (error) {
      this.logger.error(`Error downloading image: ${error.message}`);
      throw new Error('Failed to download image from OpenLibrary');
    }
  }
}
