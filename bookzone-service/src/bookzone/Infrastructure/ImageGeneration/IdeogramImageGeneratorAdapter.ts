import { Injectable, Logger } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import axios from 'axios';
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

      const response = await axios.post(
        this.apiUrl,
        {
          image_request: {
            prompt: prompt,
            aspect_ratio: 'ASPECT_1_1',
            model: 'V_2',
            magic_prompt_option: 'AUTO',
          },
        },
        {
          headers: {
            'Api-Key': this.apiKey,
            'Content-Type': 'application/json',
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
