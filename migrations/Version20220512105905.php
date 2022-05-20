<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220512105905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('TRUNCATE reach');
        $this->addSql('ALTER TABLE flight ADD universe DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE rating DROP universe');
        $this->addSql('ALTER TABLE reach DROP CONSTRAINT fk_d64339aa32efc6');
        $this->addSql('DROP INDEX idx_d64339aa32efc6');
        $this->addSql('ALTER TABLE reach RENAME COLUMN rating_id TO flight_id');
        $this->addSql('ALTER TABLE reach ADD CONSTRAINT FK_D64339A91F478C5 FOREIGN KEY (flight_id) REFERENCES flight (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D64339A91F478C5 ON reach (flight_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rating ADD universe DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE reach DROP CONSTRAINT FK_D64339A91F478C5');
        $this->addSql('DROP INDEX IDX_D64339A91F478C5');
        $this->addSql('ALTER TABLE reach RENAME COLUMN flight_id TO rating_id');
        $this->addSql('ALTER TABLE reach ADD CONSTRAINT fk_d64339aa32efc6 FOREIGN KEY (rating_id) REFERENCES rating (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_d64339aa32efc6 ON reach (rating_id)');
        $this->addSql('ALTER TABLE flight DROP universe');
    }
}
