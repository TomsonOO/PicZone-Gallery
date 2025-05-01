import { Injectable, Logger } from "@nestjs/common";
import { ConfigService } from "@nestjs/config";
import { BookAnalysisPort } from "src/bookzone/Application/Port/BookAnalysisPort";


@Injectable()
export class GeminiBookAnalyzerAdapter implements BookAnalysisPort {
  private readonly genAI: GoogleGenerativeAI;
  private readonly model: string;
  private readonly logger = new Logger(GeminiBookAnalyzerAdapter.name);

  constructor(private readonly configService: ConfigService) {
    const apiKey = this.configService.get<string>('GEMINI_API_KEY');
    if (!apiKey) {
      throw new Error('GEMINI_API_KEY not configured');
    }
    this.genAI = new GoogleGenerativeAI(apiKey);
    this.model = this.configService.get<string>('GEMINI_MODEL');
  }

  async getBookInformation(context: { title: string; author: string; }, userQuery: string): Promise<string> {
    try {
      const model = this.genAI.genGenerativeModel({ model: this.model });

      const prompt = `Book: "${context.title}" by ${context.author}
      User query: ${userQuery}
      Please provide a concise, helpful answer to the query based on your knowledge of this book.If you don't have sufficent information about this book, please state that and provide the most reasonable answer based on what you know about the book. `;

      const result = await model.generateContent(prompt);
      return result.response.text();
    } catch (error) {
      this.logger.error(`Error generating answer with Gemini: ${error.message}`, error.stack);
      throw new Error('Failed to generate book information');
    }
  }

  async generateVisualPrompt(context: { title: string; author: string; }, description: string): Promise<string> {
    try {
      const model = this.genAI.getGenerativeModel({ model: this.model });

      const prompt = `Book: "${context.title}" by ${context.author} 
      Subject for visualization: ${description} 
      Create a detailed visual description that could be used as a prompt for an image generation AI. Focus on visual elements like appearance, clothing, setting, lighting, mood, style, etc. For characters, include physical traits, attire, and expressions. For scenes, describe the environment, objects, lighting and atmosphere. Make the description specific and vivid, suitable for high-quality image generation.`;

      const result = await model.generateContent(prompt);
      return result.response.text();
    } catch (error) {
      this.logger.error(`Error generating visual prompt with Gemini: ${error.message}`, error.stack);
      throw new Error('Failed to generate visual prompt');
    }
  }

  async discoverBookElements(context: { title: string; author: string; }, elementType: "characters" | "scenes" | "themes"): Promise<string[]> {
    try {
      const model = this.genAI.getGenerativeModel({ model: this.model });

      let promptInstruction: string;

      switch (elementType) {
        case 'characters':
          promptInstruction = 'List the main and significant supporting characters in this book. Include only the character names.';
          break;
        case 'scenes':
          promptInstruction = 'List the most memorable or significant scenes from this book. Provide a brief one-phrase description for each scene.';
          break;
        case 'themes':
          promptInstruction = 'List the primary themes explored in this book. Provide each theme as a concise phrase.';
          break;
      }

      const prompt = `Book: "${context.title}" by ${context.author} ${promptInstruction} Format your response as a simple list, one item per line.`;

      const result = await model.generateContent(prompt);
      const content = result.response.text();

      return content
        .split('\n')
        .map(line => line.trim())
        .filter(line => line && !line.startsWith('-') && !line.match(/^\d+\./))
        .map(line => line.replace(/^- /, '').replace(/^\* /, ''));
    } catch (error) {
      this.logger.error(`Error discovering book elements with Gemini: ${error.message}`, error.stack);
      throw new Error(`Failed to discover book ${elementType}`);
    }
  }
} 
