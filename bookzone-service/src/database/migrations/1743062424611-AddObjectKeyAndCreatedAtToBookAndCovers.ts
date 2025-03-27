import { MigrationInterface, QueryRunner } from 'typeorm';

export class AddObjectKeyAndCreatedAtToBookAndCovers1743062424611
  implements MigrationInterface
{
  name = 'AddObjectKeyAndCreatedAtToBookAndCovers1743062424611';

  public async up(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.query(
      `ALTER TABLE "book" ADD "created_at" TIMESTAMP NOT NULL DEFAULT now()`,
    );
    await queryRunner.query(
      `ALTER TABLE "book_cover" ADD "object_key" character varying NOT NULL`,
    );
  }

  public async down(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.query(
      `ALTER TABLE "book_cover" DROP COLUMN "object_key"`,
    );
    await queryRunner.query(`ALTER TABLE "book" DROP COLUMN "created_at"`);
  }
}
