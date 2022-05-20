<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220401145825 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rating (id UUID NOT NULL, tvr DOUBLE PRECISION NOT NULL, grps20 DOUBLE PRECISION NOT NULL, affinity DOUBLE PRECISION NOT NULL, universe DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN rating.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE reach (id UUID NOT NULL, rating_id UUID NOT NULL, name VARCHAR(255) NOT NULL, percent DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D64339AA32EFC6 ON reach (rating_id)');
        $this->addSql('COMMENT ON COLUMN reach.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN reach.rating_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE reach ADD CONSTRAINT FK_D64339AA32EFC6 FOREIGN KEY (rating_id) REFERENCES rating (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE spot ADD rating_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN spot.rating_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE spot ADD CONSTRAINT FK_B9327A73A32EFC6 FOREIGN KEY (rating_id) REFERENCES rating (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B9327A73A32EFC6 ON spot (rating_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reach DROP CONSTRAINT FK_D64339AA32EFC6');
        $this->addSql('ALTER TABLE spot DROP CONSTRAINT FK_B9327A73A32EFC6');
        $this->addSql('DROP TABLE rating');
        $this->addSql('DROP TABLE reach');
        $this->addSql('DROP INDEX UNIQ_B9327A73A32EFC6');
        $this->addSql('ALTER TABLE spot DROP rating_id');
    }
}
