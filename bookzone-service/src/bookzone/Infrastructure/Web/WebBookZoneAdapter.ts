import { Controller, Post, Body } from '@nestjs/common';
import { CommandBus } from '@nestjs/cqrs';
import { CreateBookCommand } from 'src/bookzone/Application/createBook/CreateBookCommand';
import { CreateBookDTO } from 'src/bookzone/Application/createBook/CreateBookDTO';

@Controller('/')
export class WebBookZoneAdapter {
  constructor(private readonly commandBus: CommandBus) { }

  @Post('')
  async getFirstEndpoint(
    @Body() createBookDTO: CreateBookDTO
  ): Promise<string> {
    let gowno = 'testsetsetes';

    let command = new CreateBookCommand(createBookDTO);
    gowno = await this.commandBus.execute(command);

    return gowno;
  }
}
