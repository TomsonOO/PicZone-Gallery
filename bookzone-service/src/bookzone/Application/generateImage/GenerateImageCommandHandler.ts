import { IQueryHandler, QueryHandler } from '@nestjs/cqrs';
import { Inject, Logger } from '@nestjs/common';
import { GenerateImageCommand } from './GenerateImageCommand';
import { ImageGenerationPort } from '../Port/ImageGenerationPort';

@QueryHandler(GenerateImageCommand)
export class GenerateImageCommandHandler implements IQueryHandler<GenerateImageCommand, string> {
  private readonly logger = new Logger(GenerateImageCommandHandler.name);

  constructor(
    @Inject('ImageGenerationPort')
    private readonly imageGenerationPort: ImageGenerationPort,
  ) { }

  async execute(query: GenerateImageCommand): Promise<string> {
    this.logger.log(
      `Executing GenerateImageCommand for prompt: ${query.prompt.substring(0, 50)}...`,
    );

    try {
      const imageUrl = await this.imageGenerationPort.generateImage(
        query.prompt,
      );
      this.logger.log(`Image generation successful. URL: ${imageUrl}`);
      return imageUrl;
    } catch (error) {
      this.logger.error(
        `GenerateImageCommand failed: ${error.message}`,
        error.stack,
      );
      throw error;
    }
  }
}

export { GenerateImageCommandHandler as GenerateImageHandler };
