import { Injectable, Logger } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import axios from 'axios';
import * as FormData from 'form-data';
import { ImageGenerationPort } from '../../Application/Port/ImageGenerationPort';

@Injectable()
export class IdeogramImageGeneratorAdapter implements ImageGenerationPort {
  private readonly logger = new Logger(IdeogramImageGeneratorAdapter.name);
  private readonly apiKey: string;
  private readonly apiUrl: string;

  constructor(private readonly configService: ConfigService) {
    this.apiKey = this.configService.get<string>('IDEOGRAM_API_KEY', '');
    this.apiUrl = this.configService.get<string>('IDEOGRAM_API_URL', '');

    if (!this.apiKey) {
      this.logger.error('IDEOGRAM_API_KEY is not configured.');
      throw new Error('Ideogram API key is missing in configuration.');
    }
  }

  async generateImage(prompt: string): Promise<string> {
    try {
      this.logger.log(
        `Generating image for prompt: ${prompt.substring(0, 30)}...`,
      );

      const formData = new FormData();
      formData.append('prompt', prompt);
      formData.append('rendering_speed', 'DEFAULT');
      formData.append('style_type', 'GENERAL');
      formData.append('magic_prompt', 'ON');

      const response = await axios.post(
        this.apiUrl,
        formData,
        {
          headers: {
            'Api-Key': this.apiKey,
            ...formData.getHeaders(),
          },
        },
      );

      if (response?.data?.data?.[0]?.url) {
        const imageUrl = response.data.data[0].url;
        this.logger.log(`Image generated successfully: ${imageUrl}`);
        return imageUrl;
      }

      throw new Error('No image URL in API response');
    } catch (error) {
      this.logger.error(`Image generation failed: ${error.message}`);
      throw new Error('Image generation failed');
    }
  }
}
