<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220330121824 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE flight (id UUID NOT NULL, target_audience_id UUID NOT NULL, project_id UUID NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C257E60E3B6891C ON flight (target_audience_id)');
        $this->addSql('CREATE INDEX IDX_C257E60E166D1F9C ON flight (project_id)');
        $this->addSql('COMMENT ON COLUMN flight.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN flight.target_audience_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN flight.project_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE target_audience (id UUID NOT NULL, name VARCHAR(255) NOT NULL, conditions JSONB NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN target_audience.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE flight ADD CONSTRAINT FK_C257E60E3B6891C FOREIGN KEY (target_audience_id) REFERENCES target_audience (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE flight ADD CONSTRAINT FK_C257E60E166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE flight DROP CONSTRAINT FK_C257E60E3B6891C');
        $this->addSql('DROP TABLE flight');
        $this->addSql('DROP TABLE target_audience');
    }
}
