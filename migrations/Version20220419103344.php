<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220419103344 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX unique_idx');
        $this->addSql('CREATE UNIQUE INDEX unique_idx ON spot (program_id, channel_id, package_id, sponsor_type_id, month_id, week_day_id, broadcast_start, broadcast_finish, timing_in_sec, outs_per_month)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX unique_idx');
        $this->addSql('CREATE UNIQUE INDEX unique_idx ON spot (program_id, channel_id, package_id, sponsor_type_id, month_id, week_day_id, broadcast_start, broadcast_finish)');
    }
}
