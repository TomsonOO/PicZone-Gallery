import { Controller, Post, Body, HttpStatus, HttpCode } from '@nestjs/common';
import { CommandBus } from '@nestjs/cqrs';
import { CreateBookCommand } from 'src/bookzone/Application/createBook/CreateBookCommand';
import { CreateBookDTO } from 'src/bookzone/Application/createBook/CreateBookDTO';

@Controller('/books')
export class WebBookZoneAdapter {
  constructor(private readonly commandBus: CommandBus) { }
  @Post()
  @HttpCode(HttpStatus.CREATED)
  async createBook(
    @Body() createBookDTO: CreateBookDTO
  ): Promise<{ id: string }> {

    const command = new CreateBookCommand(createBookDTO);
    const bookId = await this.commandBus.execute(command);

    return { id: bookId };
  }
}
