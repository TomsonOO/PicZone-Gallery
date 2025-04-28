import { IQueryHandler, QueryHandler } from "@nestjs/cqrs";
import { GetBookCoverPresignedUrlQuery } from "./GetBookCoverPresignedUrlQuery";
import { Inject } from "@nestjs/common";
import { BookStoragePort } from "../Port/BookStoragePort";

@QueryHandler(GetBookCoverPresignedUrlQuery)
export class GetBookCoverPresignedUrlQueryHandler implements IQueryHandler<GetBookCoverPresignedUrlQuery> {
  constructor(
    @Inject('S3StorageService')
    private readonly storageService: BookStoragePort,
  ) { }

  async execute(query: GetBookCoverPresignedUrlQuery): Promise<string> {
    try {
      const presignedUrl = await this.storageService.getPresignedUrl(query.objectKey);
      return presignedUrl;
    } catch (error) {
      throw new Error(`Failed to generate presigned URL: ${error.message}`);
    }
  }
}
