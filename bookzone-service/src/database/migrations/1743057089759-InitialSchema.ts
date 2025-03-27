import { MigrationInterface, QueryRunner } from 'typeorm';

export class InitialSchema1743057089759 implements MigrationInterface {
  name = 'InitialSchema1743057089759';

  public async up(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.query(
      `CREATE TABLE "book" ("id" uuid NOT NULL DEFAULT uuid_generate_v4(), "title" character varying NOT NULL, "author" character varying NOT NULL, CONSTRAINT "PK_a3afef72ec8f80e6e5c310b28a4" PRIMARY KEY ("id"))`,
    );
    await queryRunner.query(
      `CREATE TABLE "book_cover" ("id" uuid NOT NULL DEFAULT uuid_generate_v4(), "url" character varying NOT NULL, "bookId" uuid, CONSTRAINT "REL_5a2792d1c6e934174cb0064d57" UNIQUE ("bookId"), CONSTRAINT "PK_33a2794b6e579f870f1be2fa254" PRIMARY KEY ("id"))`,
    );
    await queryRunner.query(
      `ALTER TABLE "book_cover" ADD CONSTRAINT "FK_5a2792d1c6e934174cb0064d57a" FOREIGN KEY ("bookId") REFERENCES "book"("id") ON DELETE NO ACTION ON UPDATE NO ACTION`,
    );
  }

  public async down(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.query(
      `ALTER TABLE "book_cover" DROP CONSTRAINT "FK_5a2792d1c6e934174cb0064d57a"`,
    );
    await queryRunner.query(`DROP TABLE "book_cover"`);
    await queryRunner.query(`DROP TABLE "book"`);
  }
}
