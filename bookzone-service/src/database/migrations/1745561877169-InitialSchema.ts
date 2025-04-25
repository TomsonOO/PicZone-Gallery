import { MigrationInterface, QueryRunner } from 'typeorm';

export class InitialSchema1745561877169 implements MigrationInterface {
  name = 'InitialSchema1745561877169';

  public async up(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.query(
      `ALTER TABLE "book_cover" RENAME COLUMN "object_key" TO "objectKey"`,
    );
    await queryRunner.query(
      `ALTER TABLE "book" ADD "openLibraryKey" character varying`,
    );
  }

  public async down(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.query(`ALTER TABLE "book" DROP COLUMN "openLibraryKey"`);
    await queryRunner.query(
      `ALTER TABLE "book_cover" RENAME COLUMN "objectKey" TO "object_key"`,
    );
  }
}
