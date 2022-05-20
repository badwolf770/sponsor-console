<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220331083128 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE flight DROP CONSTRAINT fk_c257e60e166d1f9c');
        $this->addSql('DROP INDEX idx_c257e60e166d1f9c');
        $this->addSql('ALTER TABLE flight RENAME COLUMN project_id TO package_id');
        $this->addSql('ALTER TABLE flight ADD CONSTRAINT FK_C257E60EF44CABFF FOREIGN KEY (package_id) REFERENCES package (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_C257E60EF44CABFF ON flight (package_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE flight DROP CONSTRAINT FK_C257E60EF44CABFF');
        $this->addSql('DROP INDEX IDX_C257E60EF44CABFF');
        $this->addSql('ALTER TABLE flight RENAME COLUMN package_id TO project_id');
        $this->addSql('ALTER TABLE flight ADD CONSTRAINT fk_c257e60e166d1f9c FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_c257e60e166d1f9c ON flight (project_id)');
    }
}
