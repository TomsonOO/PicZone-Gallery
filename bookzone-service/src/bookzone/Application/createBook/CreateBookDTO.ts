import { IsNotEmpty, IsString, MinLength } from 'class-validator';

export class CreateBookDTO {
  @IsString()
  @IsNotEmpty()
  @MinLength(1)
  title: string;

  @IsString()
  @IsNotEmpty()
  @MinLength(1)
  author: string;

  @IsString()
  @IsNotEmpty()
  url: string;

  @IsString()
  @IsNotEmpty()
  objectKey: string;
}
