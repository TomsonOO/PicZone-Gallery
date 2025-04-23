import { Controller, Post, Body, HttpStatus, HttpCode, Get } from '@nestjs/common';
import { CommandBus, QueryBus } from '@nestjs/cqrs';
import { CreateBookCommand } from 'src/bookzone/Application/createBook/CreateBookCommand';
import { CreateBookDTO } from 'src/bookzone/Application/createBook/CreateBookDTO';
import { GetBooksQuery } from 'src/bookzone/Application/getBooks/GetBooksQuery';
import { BookDto } from 'src/bookzone/Application/getBooks/BookDto';

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

}
