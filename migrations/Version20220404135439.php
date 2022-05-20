<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220404135439 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_8d93d649187d9ed4');
        $this->addSql('ALTER TABLE "user" ADD surname VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" RENAME COLUMN auth_key TO email');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('ALTER TABLE messenger_messages ALTER queue_name TYPE VARCHAR(190)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('ALTER TABLE "user" DROP surname');
        $this->addSql('ALTER TABLE "user" RENAME COLUMN email TO auth_key');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d649187d9ed4 ON "user" (auth_key)');
        $this->addSql('ALTER TABLE messenger_messages ALTER queue_name TYPE VARCHAR(255)');
    }
}
