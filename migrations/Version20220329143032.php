<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220329143032 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE channel (id UUID NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A2F98E475E237E06 ON channel (name)');
        $this->addSql('COMMENT ON COLUMN channel.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN channel.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE file (id UUID NOT NULL, name VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, web_path VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN file.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE month (id UUID NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8EB610065E237E06 ON month (name)');
        $this->addSql('COMMENT ON COLUMN month.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE package (id UUID NOT NULL, project_id UUID NOT NULL, name VARCHAR(255) NOT NULL, tax DOUBLE PRECISION NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DE686795166D1F9C ON package (project_id)');
        $this->addSql('COMMENT ON COLUMN package.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN package.project_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN package.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE program (id UUID NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_92ED77845E237E06 ON program (name)');
        $this->addSql('COMMENT ON COLUMN program.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN program.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE project (id UUID NOT NULL, file_id UUID DEFAULT NULL, client VARCHAR(255) NOT NULL, brand VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2FB3D0EE93CB796C ON project (file_id)');
        $this->addSql('COMMENT ON COLUMN project.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN project.file_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN project.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE sponsor_type (id UUID NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6C7C14C95E237E06 ON sponsor_type (name)');
        $this->addSql('COMMENT ON COLUMN sponsor_type.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE spot (id UUID NOT NULL, program_id UUID NOT NULL, sponsor_type_id UUID DEFAULT NULL, month_id UUID NOT NULL, week_day_id UUID NOT NULL, package_id UUID NOT NULL, channel_id UUID NOT NULL, broadcast_start INT NOT NULL, broadcast_finish INT NOT NULL, timing_in_sec INT NOT NULL, outs_per_month INT NOT NULL, cost DOUBLE PRECISION NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B9327A733EB8070A ON spot (program_id)');
        $this->addSql('CREATE INDEX IDX_B9327A738F2282D1 ON spot (sponsor_type_id)');
        $this->addSql('CREATE INDEX IDX_B9327A73A0CBDE4 ON spot (month_id)');
        $this->addSql('CREATE INDEX IDX_B9327A737DB83875 ON spot (week_day_id)');
        $this->addSql('CREATE INDEX IDX_B9327A73F44CABFF ON spot (package_id)');
        $this->addSql('CREATE INDEX IDX_B9327A7372F5A1AA ON spot (channel_id)');
        $this->addSql('CREATE UNIQUE INDEX unique_idx ON spot (program_id, channel_id, package_id, sponsor_type_id, month_id, week_day_id, broadcast_start, broadcast_finish)');
        $this->addSql('COMMENT ON COLUMN spot.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN spot.program_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN spot.sponsor_type_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN spot.month_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN spot.week_day_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN spot.package_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN spot.channel_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN spot.broadcast_start IS \'время начала, без разделителя :, пример 800 = 08:00\'');
        $this->addSql('COMMENT ON COLUMN spot.broadcast_finish IS \'время окончания, без разделителя :, пример 1500 = 15:00\'');
        $this->addSql('COMMENT ON COLUMN spot.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE template_import (id UUID NOT NULL, project_id UUID NOT NULL, template_type_id UUID NOT NULL, file_path VARCHAR(255) NOT NULL, status INT NOT NULL, started_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, finished_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, original_file_name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_96A6C7EF166D1F9C ON template_import (project_id)');
        $this->addSql('CREATE INDEX IDX_96A6C7EF96F4F7AA ON template_import (template_type_id)');
        $this->addSql('COMMENT ON COLUMN template_import.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN template_import.project_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN template_import.template_type_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN template_import.started_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN template_import.finished_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN template_import.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE template_type (id UUID NOT NULL, name VARCHAR(255) NOT NULL, class VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN template_type.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, name VARCHAR(255) NOT NULL, auth_key VARCHAR(180) NOT NULL, roles JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649187D9ED4 ON "user" (auth_key)');
        $this->addSql('CREATE TABLE week_day (id UUID NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_256D13615E237E06 ON week_day (name)');
        $this->addSql('COMMENT ON COLUMN week_day.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE package ADD CONSTRAINT FK_DE686795166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE93CB796C FOREIGN KEY (file_id) REFERENCES file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE spot ADD CONSTRAINT FK_B9327A733EB8070A FOREIGN KEY (program_id) REFERENCES program (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE spot ADD CONSTRAINT FK_B9327A738F2282D1 FOREIGN KEY (sponsor_type_id) REFERENCES sponsor_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE spot ADD CONSTRAINT FK_B9327A73A0CBDE4 FOREIGN KEY (month_id) REFERENCES month (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE spot ADD CONSTRAINT FK_B9327A737DB83875 FOREIGN KEY (week_day_id) REFERENCES week_day (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE spot ADD CONSTRAINT FK_B9327A73F44CABFF FOREIGN KEY (package_id) REFERENCES package (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE spot ADD CONSTRAINT FK_B9327A7372F5A1AA FOREIGN KEY (channel_id) REFERENCES channel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE template_import ADD CONSTRAINT FK_96A6C7EF166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE template_import ADD CONSTRAINT FK_96A6C7EF96F4F7AA FOREIGN KEY (template_type_id) REFERENCES template_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE spot DROP CONSTRAINT FK_B9327A7372F5A1AA');
        $this->addSql('ALTER TABLE project DROP CONSTRAINT FK_2FB3D0EE93CB796C');
        $this->addSql('ALTER TABLE spot DROP CONSTRAINT FK_B9327A73A0CBDE4');
        $this->addSql('ALTER TABLE spot DROP CONSTRAINT FK_B9327A73F44CABFF');
        $this->addSql('ALTER TABLE spot DROP CONSTRAINT FK_B9327A733EB8070A');
        $this->addSql('ALTER TABLE package DROP CONSTRAINT FK_DE686795166D1F9C');
        $this->addSql('ALTER TABLE template_import DROP CONSTRAINT FK_96A6C7EF166D1F9C');
        $this->addSql('ALTER TABLE spot DROP CONSTRAINT FK_B9327A738F2282D1');
        $this->addSql('ALTER TABLE template_import DROP CONSTRAINT FK_96A6C7EF96F4F7AA');
        $this->addSql('ALTER TABLE spot DROP CONSTRAINT FK_B9327A737DB83875');
        $this->addSql('DROP TABLE channel');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE month');
        $this->addSql('DROP TABLE package');
        $this->addSql('DROP TABLE program');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE sponsor_type');
        $this->addSql('DROP TABLE spot');
        $this->addSql('DROP TABLE template_import');
        $this->addSql('DROP TABLE template_type');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE week_day');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
