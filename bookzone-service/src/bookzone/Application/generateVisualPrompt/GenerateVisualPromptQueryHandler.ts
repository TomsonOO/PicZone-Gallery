import { IQueryHandler, QueryHandler } from "@nestjs/cqrs";
import { GenerateVisualPromptQuery } from "./GenerateVisualPromptQuery";
import { BookAnalysisPort } from "../Port/BookAnalysisPort";
import { Inject, Logger } from "@nestjs/common";

@QueryHandler(GenerateVisualPromptQuery)
export class GenerateVisualPromptQueryHandler implements IQueryHandler<GenerateVisualPromptQuery, string> {
  private readonly logger = new Logger(GenerateVisualPromptQueryHandler.name);

  constructor(
    @Inject('BookAnalysisPort')
    private readonly bookAnalysisPort: BookAnalysisPort,
  ) {}

  async execute(query: GenerateVisualPromptQuery): Promise<string> {
    this.logger.log(`Generating visual prompt for "${query.subject}" in book "${query.title}" by ${query.author}`);

    try {
      const context = {
        title: query.title,
        author: query.author,
      };

      const visualPrompt = await this.bookAnalysisPort.generateVisualPrompt(context, query.subject);
      return visualPrompt;
    } catch (error) {
      this.logger.error(`Failed to generate visual prompt: ${error.message}`, error.stack);
      throw new Error('Failed to generate visual prompt');
    }
  }
}
