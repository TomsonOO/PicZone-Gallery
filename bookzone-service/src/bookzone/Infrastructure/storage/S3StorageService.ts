import {
  GetObjectCommand,
  HeadObjectCommand,
  PutObjectCommand,
  S3Client,
} from '@aws-sdk/client-s3';
import { getSignedUrl } from '@aws-sdk/s3-request-presigner';
import { Injectable, Logger } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import { BookStoragePort } from 'src/bookzone/Application/Port/BookStoragePort';

@Injectable()
export class S3StorageService implements BookStoragePort {
  private s3Client: S3Client;
  private bucketName: string;
  private readonly logger = new Logger(S3StorageService.name);

  constructor(private configService: ConfigService) {
    const region = this.configService.get<string>('AWS_S3_REGION');
    const accessKeyId = this.configService.get<string>('AWS_ACCESS_KEY_ID');
    const secretAccessKey = this.configService.get<string>(
      'AWS_SECRET_ACCESS_KEY',
    );
    const bucketName = this.configService.get<string>('AWS_S3_BUCKET_NAME');

    if (!region || !accessKeyId || !secretAccessKey || !bucketName) {
      this.logger.error(
        'Missing AWS configuration. Please check environment variables.',
      );
      throw new Error('Missing AWS configuration');
    }

    this.s3Client = new S3Client({
      region,
      credentials: {
        accessKeyId,
        secretAccessKey,
      },
    });

    this.bucketName = bucketName;
  }

  async uploadFile(
    fileBuffer: Buffer,
    fileName: string,
    contentType: string,
    directory: string = '',
  ): Promise<string> {
    try {
      const key = directory ? `${directory}/${fileName}` : fileName;
      const command = new PutObjectCommand({
        Bucket: this.bucketName,
        Key: key,
        Body: fileBuffer,
        ContentType: contentType,
      });

      await this.s3Client.send(command);

      const fileUrl = `https://${this.bucketName}.s3.amazonaws.com/${key}`;
      return fileUrl;
    } catch (error) {
      this.logger.error(
        `Error uploading file to S3: ${error.message}`,
        error.stack,
      );
      throw new Error('Failed to upload file to S3');
    }
  }

  async getPresignedUrl(objectKey: string): Promise<string> {
    try {
      const headCommand = new HeadObjectCommand({
        Bucket: this.bucketName,
        Key: objectKey,
      });
      await this.s3Client.send(headCommand);

      const command = new GetObjectCommand({
        Bucket: this.bucketName,
        Key: objectKey,
      });

      const presignedUrl = await getSignedUrl(this.s3Client, command, {
        expiresIn: 1200,
      });
      return presignedUrl;
    } catch (error) {
      this.logger.error(
        `Error generating presigned URL: ${error.message}`,
        error.stack,
      );
      throw new Error('Failed to generate presgined URL for book cover');
    }
  }
}
