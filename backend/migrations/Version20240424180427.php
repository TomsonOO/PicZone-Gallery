<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240424180427 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" ADD profile_image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD biography TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD is_profile_public BOOLEAN DEFAULT true NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD settings JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649C4CF44DC FOREIGN KEY (profile_image_id) REFERENCES image (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649C4CF44DC ON "user" (profile_image_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649C4CF44DC');
        $this->addSql('DROP INDEX UNIQ_8D93D649C4CF44DC');
        $this->addSql('ALTER TABLE "user" DROP profile_image_id');
        $this->addSql('ALTER TABLE "user" DROP biography');
        $this->addSql('ALTER TABLE "user" DROP is_profile_public');
        $this->addSql('ALTER TABLE "user" DROP settings');
    }
}
