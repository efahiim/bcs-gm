<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230629225447 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE order_details (id INT AUTO_INCREMENT NOT NULL, order_product_id INT NOT NULL, quantity INT NOT NULL, product VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, total_price DOUBLE PRECISION NOT NULL, INDEX IDX_845CA2C1F65E9B0F (order_product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_details ADD CONSTRAINT FK_845CA2C1F65E9B0F FOREIGN KEY (order_product_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE `order` CHANGE status status ENUM(\'Paid\', \'Delivered\')');
        $this->addSql('ALTER TABLE product CHANGE type type ENUM(\'Game\', \'Device\')');
        $this->addSql('ALTER TABLE request CHANGE status status ENUM(\'Pending\', \'Approved\', \'Rejected\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_details DROP FOREIGN KEY FK_845CA2C1F65E9B0F');
        $this->addSql('DROP TABLE order_details');
        $this->addSql('ALTER TABLE `order` CHANGE status status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE product CHANGE type type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE request CHANGE status status VARCHAR(255) DEFAULT NULL');
    }
}
