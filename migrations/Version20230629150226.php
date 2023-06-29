<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230629150226 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message ADD reply VARCHAR(255) DEFAULT NULL, ADD replied_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE product CHANGE type type ENUM(\'Game\', \'Device\')');
        $this->addSql('ALTER TABLE request CHANGE status status ENUM(\'Pending\', \'Approved\', \'Rejected\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP reply, DROP replied_at');
        $this->addSql('ALTER TABLE product CHANGE type type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE request CHANGE status status VARCHAR(255) DEFAULT NULL');
    }
}
