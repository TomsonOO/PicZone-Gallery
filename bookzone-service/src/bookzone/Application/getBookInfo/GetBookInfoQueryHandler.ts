import { IQueryHandler, QueryHandler } from "@nestjs/cqrs";
import { GetBookInfoQuery } from "./GetBookInfoQuery";
import { BookAnalysisPort } from "../Port/BookAnalysisPort";
import { Inject, Logger } from "@nestjs/common";

@QueryHandler(GetBookInfoQuery)
export class GetBookInfoQueryHandler implements IQueryHandler<GetBookInfoQuery, string> {

  private readonly logger = new Logger(GetBookInfoQueryHandler.name);

  constructor(
    @Inject('BookAnalysisPort')
    private readonly bookAnalysisPort: BookAnalysisPort,
  ) { }

  async execute(query: GetBookInfoQuery): Promise<string> {
    this.logger.log(`Processing book information query for "${query.title}" by ${query.author}: ${query.query}`);

    try {
      const context = {
        title: query.title,
        author: query.author,
      };

      const answer = await this.bookAnalysisPort.getBookInformation(context, query.query);
      return answer;
    } catch(error) {
      this.logger.error(`Failed to get book information: ${error.message}`, error.stack);
      throw new Error('Failed to retrieve book information');
    }
  }
}
