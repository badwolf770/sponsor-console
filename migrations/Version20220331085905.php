<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220331085905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE spot ADD flight_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN spot.flight_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE spot ADD CONSTRAINT FK_B9327A7391F478C5 FOREIGN KEY (flight_id) REFERENCES flight (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B9327A7391F478C5 ON spot (flight_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE spot DROP CONSTRAINT FK_B9327A7391F478C5');
        $this->addSql('DROP INDEX IDX_B9327A7391F478C5');
        $this->addSql('ALTER TABLE spot DROP flight_id');
    }
}
