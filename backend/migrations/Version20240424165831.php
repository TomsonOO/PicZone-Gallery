<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240424165831 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add type column to image table.';
    }

    public function up(Schema $schema): void
    {
        // Add only the new column if it is indeed new and necessary.
        $this->addSql('ALTER TABLE image ADD type VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // Provide a way to revert the change.
        $this->addSql('ALTER TABLE image DROP COLUMN type');
    }
}
