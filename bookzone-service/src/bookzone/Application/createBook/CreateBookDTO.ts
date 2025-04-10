import { IsString } from 'class-validator';

export class CreateBookDTO {

  @IsString()
  title: string;

  @IsString()
  author: string;

  @IsString()
  url: string;

  @IsString()
  objectKey: string;
}
