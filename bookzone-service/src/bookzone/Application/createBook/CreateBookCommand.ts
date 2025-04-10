import { CreateBookDTO } from "./CreateBookDTO";

export class CreateBookCommand {
  public readonly title: string;
  public readonly author: string;
  public readonly url: string;
  public readonly objectKey: string;

  constructor(createBookDTO: CreateBookDTO) {
    this.title = createBookDTO.title;
    this.author = createBookDTO.author;
    this.url = createBookDTO.url;
    this.objectKey = createBookDTO.objectKey;
  }
}
