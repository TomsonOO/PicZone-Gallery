<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241227082401 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE images_likes_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "image_like" (id INT NOT NULL, user_id INT NOT NULL, image_id INT NOT NULL, liked_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5E59AA2DA76ED395 ON "image_like" (user_id)');
        $this->addSql('CREATE INDEX IDX_5E59AA2D3DA5256D ON "image_like" (image_id)');
        $this->addSql('CREATE UNIQUE INDEX user_image_like_unique ON "image_like" (user_id, image_id)');
        $this->addSql('COMMENT ON COLUMN "image_like".liked_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE "image_like" ADD CONSTRAINT FK_5E59AA2DA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "image_like" ADD CONSTRAINT FK_5E59AA2D3DA5256D FOREIGN KEY (image_id) REFERENCES "image" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE image ADD tags JSON DEFAULT \'[]\' NOT NULL');
        $this->addSql('ALTER TABLE image ADD like_count INT DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE images_likes_id_seq CASCADE');
        $this->addSql('ALTER TABLE "image_like" DROP CONSTRAINT FK_5E59AA2DA76ED395');
        $this->addSql('ALTER TABLE "image_like" DROP CONSTRAINT FK_5E59AA2D3DA5256D');
        $this->addSql('DROP TABLE "image_like"');
        $this->addSql('ALTER TABLE "image" DROP tags');
        $this->addSql('ALTER TABLE "image" DROP like_count');
    }
}
