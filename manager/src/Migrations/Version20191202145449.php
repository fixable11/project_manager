<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191202145449 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE work_members_groups (id UUID NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN work_members_groups.id IS \'(DC2Type:work_members_group_id)\'');
        $this->addSql('ALTER TABLE user_users ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE user_users ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE user_users ALTER email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_users ALTER email DROP DEFAULT');
        $this->addSql('ALTER TABLE user_users ALTER role TYPE VARCHAR(16)');
        $this->addSql('ALTER TABLE user_users ALTER role DROP DEFAULT');
        $this->addSql('ALTER TABLE user_users ALTER new_email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_users ALTER new_email DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN user_users.id IS \'(DC2Type:user_user_id)\'');
        $this->addSql('COMMENT ON COLUMN user_users.email IS \'(DC2Type:user_user_email)\'');
        $this->addSql('COMMENT ON COLUMN user_users.role IS \'(DC2Type:user_user_role)\'');
        $this->addSql('COMMENT ON COLUMN user_users.new_email IS \'(DC2Type:user_user_email)\'');
        $this->addSql('ALTER TABLE user_user_networks ALTER user_id TYPE UUID');
        $this->addSql('ALTER TABLE user_user_networks ALTER user_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN user_user_networks.user_id IS \'(DC2Type:user_user_id)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE work_members_groups');
        $this->addSql('ALTER TABLE user_user_networks ALTER user_id TYPE UUID');
        $this->addSql('ALTER TABLE user_user_networks ALTER user_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN user_user_networks.user_id IS NULL');
        $this->addSql('ALTER TABLE user_users ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE user_users ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE user_users ALTER email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_users ALTER email DROP DEFAULT');
        $this->addSql('ALTER TABLE user_users ALTER role TYPE VARCHAR(16)');
        $this->addSql('ALTER TABLE user_users ALTER role DROP DEFAULT');
        $this->addSql('ALTER TABLE user_users ALTER new_email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_users ALTER new_email DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN user_users.id IS NULL');
        $this->addSql('COMMENT ON COLUMN user_users.email IS NULL');
        $this->addSql('COMMENT ON COLUMN user_users.role IS NULL');
        $this->addSql('COMMENT ON COLUMN user_users.new_email IS NULL');
    }
}
