<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230629221704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, reference VARCHAR(255) NOT NULL, delivery_address LONGTEXT NOT NULL, ordered_on DATETIME NOT NULL, status ENUM(\'Paid\', \'Delivered\'), INDEX IDX_F5299398A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE product CHANGE type type ENUM(\'Game\', \'Device\')');
        $this->addSql('ALTER TABLE request CHANGE status status ENUM(\'Pending\', \'Approved\', \'Rejected\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('ALTER TABLE product CHANGE type type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE request CHANGE status status VARCHAR(255) DEFAULT NULL');
    }
}
