export interface BookStoragePort {

  uploadFile(
    fileBuffer: Buffer,
    fileName: string,
    contentType: string,
    directory: string,
  ): Promise<string>;

  getPresignedUrl(objectKey: string): Promise<string>;
}
