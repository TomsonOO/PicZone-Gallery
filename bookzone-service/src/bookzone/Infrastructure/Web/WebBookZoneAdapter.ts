import { Controller, Get } from '@nestjs/common';


@Controller('/')
export class WebBookZoneAdapter
{
  @Get('')
  async getFirstEndpoint(): Promise<string>
    {
      return "Good morning";
    }
}