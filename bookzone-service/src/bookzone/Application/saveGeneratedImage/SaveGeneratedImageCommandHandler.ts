import { CommandHandler, ICommandHandler } from "@nestjs/cqrs";
import { SaveGeneratedImageCommand } from "./SaveGeneratedImageCommand";
import { Inject, Logger } from "@nestjs/common";
import { BookVisualizationRepositoryPort } from "../Port/BookVisualizationRepositoryPort";
import { BookStoragePort } from "../Port/BookStoragePort";
import axios from "axios";
import { v4 as uuidv4 } from 'uuid';

@CommandHandler(SaveGeneratedImageCommand)
export class SaveGeneratedImageCommandHandler implements ICommandHandler<SaveGeneratedImageCommand> {

  private readonly logger = new Logger(SaveGeneratedImageCommandHandler.name);

  constructor(
    @Inject('BookVisualizationRepositoryPostgresAdapter')
    private readonly bookVisualizationRepository: BookVisualizationRepositoryPort,

    @Inject('S3StorageService')
    private readonly storageService: BookStoragePort,
  ) { }

  async execute(command: SaveGeneratedImageCommand): Promise<string> {
    this.logger.log(
      `Saving generated ${command.type} image for book ${command.bookId}`,
    );

    try {
      const response = await axios.get(command.imageUrl, {
        responseType: 'arraybuffer',
      });

      const imageBuffer = Buffer.from(response.data);
      const filename = `${uuidv4()}.jpg`;

      const directory = command.type === 'character' ? 'BookCharacters' : 'BookScenes';
      const s3Url = await this.storageService.uploadFile(
        imageBuffer,
        filename,
        'image/jpeg',
        directory,
      );

      const visualization = await this.bookVisualizationRepository.save({
        bookId: command.bookId,
        type: command.type,
        description: command.description,
        url: s3Url,
        objectKey: `${directory}/${filename}`,
      });

      return visualization.id;
    } catch (error) {
      this.logger.error(
        `Failed to save generated image: ${error.message}`,
        error.stack,
      );
      throw new Error('Failed to save generated image');
    }
  }
}
