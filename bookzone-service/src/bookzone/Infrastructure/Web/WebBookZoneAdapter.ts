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
  BadRequestException,
  InternalServerErrorException,
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
import { GenerateImageCommand } from 'src/bookzone/Application/generateImage/GenerateImageCommand';
import { GetBookInfoQuery } from 'src/bookzone/Application/getBookInfo/GetBookInfoQuery';
import { GenerateVisualPromptQuery } from 'src/bookzone/Application/generateVisualPrompt/GenerateVisualPromptQuery';
import { getBookDetailsQuery } from 'src/bookzone/Application/getBookDetails/GetBookDetailsQuery';
import { SaveGeneratedImageCommand } from 'src/bookzone/Application/saveGeneratedImage/SaveGeneratedImageCommand';
import { VisualizationType } from 'src/bookzone/Domain/VisualizationType';

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

  @Get('covers/:objectKey/presigned-url')
  async getBookCoverPresignedUrl(
    @Param('objectKey') objectKey: string,
  ): Promise<{ presignedUrl: string }> {
    try {
      const fullObjectKey = objectKey.includes('/')
        ? objectKey
        : `BookCovers/${objectKey}`;
      return await this.queryBus.execute(
        new GetBookCoverPresignedUrlQuery(fullObjectKey),
      );
    } catch (error) {
      throw new NotFoundException(
        `Could not generate presigned URL for objectKey: ${objectKey}`,
      );
    }
  }

  @Get('details/:bookId')
  async getBookDetails(@Param('bookId') bookId: string): Promise<BookDto> {
    try {
      return await this.queryBus.execute(new getBookDetailsQuery(bookId));
    } catch (error) {
      throw new NotFoundException(`Could not find the book with id: ${bookId}`);
    }
  }

  @Post('ai/generate-image')
  @HttpCode(HttpStatus.OK)
  async generateImage(
    @Body() body: { imagePrompt: string },
  ): Promise<{ imageUrl: string }> {
    if (!body.imagePrompt || body.imagePrompt.trim() === '') {
      throw new BadRequestException('Prompt is required for image generation');
    }

    const command = new GenerateImageCommand(body.imagePrompt);
    const imageUrl = await this.queryBus.execute<GenerateImageCommand, string>(
      command,
    );

    return { imageUrl };
  }

  @Post('ai/query-book')
  @HttpCode(HttpStatus.OK)
  async queryBook(
    @Body() body: { title: string; author: string; query: string },
  ): Promise<{ answer: string }> {
    if (!body.title || !body.author || !body.query) {
      throw new BadRequestException(
        'Title, author, and query are required fields',
      );
    }

    const bookInfoQuery = new GetBookInfoQuery(
      body.title,
      body.author,
      body.query,
    );
    const answer = await this.queryBus.execute<GetBookInfoQuery, string>(
      bookInfoQuery,
    );

    return { answer };
  }

  @Post('ai/generate-visual-prompt')
  @HttpCode(HttpStatus.OK)
  async generateVisualPrompt(
    @Body() body: { title: string; author: string; subject: string },
  ): Promise<{ imagePrompt: string }> {
    if (!body.title || !body.author || !body.subject) {
      throw new BadRequestException(
        'Title, author, and subject are required fields',
      );
    }

    const query = new GenerateVisualPromptQuery(
      body.title,
      body.author,
      body.subject,
    );
    const imagePrompt = await this.queryBus.execute<
      GenerateVisualPromptQuery,
      string
    >(query);

    return { imagePrompt };
  }

  @Post(':id/visualizations')
  async saveGeneratedImage(
    @Param('id') bookId: string,
    @Body() body: {
      type: string;
      description: string;
      imageUrl: string;
    },
  ) {
    try {
      if (!['character', 'scene'].includes(body.type)) {
        throw new BadRequestException('Invalid visualization type');
      }

      const command = new SaveGeneratedImageCommand(
        bookId,
        body.type as VisualizationType,
        body.description,
        body.imageUrl,
      );

      const visualizationId = await this.commandBus.execute(command);

      return { id: visualizationId };
    } catch (error) {
      throw new InternalServerErrorException('Failed to save generated image');
    }
  }
}
