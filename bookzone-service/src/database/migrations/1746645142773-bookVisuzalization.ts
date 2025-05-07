import { MigrationInterface, QueryRunner } from "typeorm";

export class BookVisuzalization1746645142773 implements MigrationInterface {
    name = 'BookVisuzalization1746645142773'

    public async up(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.query(`CREATE TABLE "book_visualization" ("id" uuid NOT NULL DEFAULT uuid_generate_v4(), "type" character varying NOT NULL, "description" text, "url" character varying NOT NULL, "objectKey" character varying NOT NULL, "created_at" TIMESTAMP NOT NULL DEFAULT now(), "bookId" uuid, CONSTRAINT "PK_4730babaf61b1da05108315b5f3" PRIMARY KEY ("id"))`);
        await queryRunner.query(`ALTER TABLE "book_visualization" ADD CONSTRAINT "FK_fbe9f3d2ff7f10a248c87b85073" FOREIGN KEY ("bookId") REFERENCES "book"("id") ON DELETE CASCADE ON UPDATE NO ACTION`);
    }

    public async down(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.query(`ALTER TABLE "book_visualization" DROP CONSTRAINT "FK_fbe9f3d2ff7f10a248c87b85073"`);
        await queryRunner.query(`DROP TABLE "book_visualization"`);
    }

}
