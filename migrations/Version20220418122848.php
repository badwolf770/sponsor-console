<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220418122848 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'insert into template_type ("id", "name", "class") 
values (\'c83743a8-c68d-4d06-97c9-80862afd4fb0\',\'everest\', \'App\Project\Application\TemplateImport\Strategy\EverestTemplateImportStrategy\')');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('delete from template_type where id = \'c83743a8-c68d-4d06-97c9-80862afd4fb0\'');
    }
}
