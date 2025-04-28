import {
  Controller,
  Post,
  Body,
  HttpStatus,
  HttpCode,
  Get,
  Query,
  NotFoundException,
  Param,
} from '@nestjs/common';
import { CommandBus, QueryBus } from '@nestjs/cqrs';
import { CreateBookCommand } from 'src/bookzone/Application/createBook/CreateBookCommand';
import { CreateBookDTO } from 'src/bookzone/Application/createBook/CreateBookDTO';
import { GetBooksQuery } from 'src/bookzone/Application/getBooks/GetBooksQuery';
import { BookDto } from 'src/bookzone/Application/getBooks/BookDto';
import { BookSearchResultDto } from 'src/bookzone/Application/searchBooks/BookSearchResultDto';
import { SearchBooksQuery } from 'src/bookzone/Application/searchBooks/SearchBooksQuery';
import { ImportBookCommand } from 'src/bookzone/Application/importBook/ImportBookCommand';
import { GetBookCoverPresignedUrlQuery } from 'src/bookzone/Application/getBookCoverPresignedUrl/GetBookCoverPresignedUrlQuery';

@Controller('/books')
export class WebBookZoneAdapter {
  constructor(
    private readonly commandBus: CommandBus,
    private readonly queryBus: QueryBus,
  ) { }

  @Post()
  @HttpCode(HttpStatus.CREATED)
  async createBook(
    @Body() createBookDTO: CreateBookDTO,
  ): Promise<{ id: string }> {
    const command = new CreateBookCommand(createBookDTO);
    const bookId = await this.commandBus.execute(command);

    return { id: bookId };
  }

  @Get()
  async getBooks(): Promise<BookDto[]> {
    return await this.queryBus.execute(new GetBooksQuery());
  }

  @Get('search')
  async searchBooks(@Query('q') query: string): Promise<BookSearchResultDto[]> {
    return await this.queryBus.execute(new SearchBooksQuery(query));
  }

  @Post('import')
  @HttpCode(HttpStatus.CREATED)
  async importBook(
    @Body() body: { openLibraryKey: string },
  ): Promise<{ id: string }> {
    const command = new ImportBookCommand(body.openLibraryKey);
    const bookId = await this.commandBus.execute(command);

    return { id: bookId };
  }

  @Get('covers')
  async getBookCoverPresignedUrl(
    @Query('objectKey') objectKey: string
  ): Promise<{ presignedUrl: string }> {
    try {
      const fullObjectKey = `BookCovers/${objectKey}`;
      console.log(`Processing presigned URL request for object: ${fullObjectKey}`);
      const presignedUrl = await this.queryBus.execute(new GetBookCoverPresignedUrlQuery(fullObjectKey));
      return { presignedUrl };
    } catch (error) {
      throw new NotFoundException(`Could not generate presigned URL for objectKey: ${objectKey}`);
    }
  }
}
