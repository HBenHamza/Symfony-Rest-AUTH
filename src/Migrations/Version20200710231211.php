<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200710231211 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE club (id INT AUTO_INCREMENT NOT NULL, kindergarten_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, created_date DATETIME NOT NULL, updated_date DATETIME NOT NULL, INDEX IDX_B8EE3872B9C0D867 (kindergarten_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE kindergarten (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, doucment_evidence_tva VARCHAR(255) NOT NULL, rating_note INT NOT NULL, description LONGTEXT NOT NULL, avalability INT NOT NULL, address LONGTEXT NOT NULL, tags LONGTEXT NOT NULL, capacity VARCHAR(255) NOT NULL, nb_children_registered INT NOT NULL, picture VARCHAR(255) NOT NULL, created_date DATETIME NOT NULL, INDEX IDX_B53C375B7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE club ADD CONSTRAINT FK_B8EE3872B9C0D867 FOREIGN KEY (kindergarten_id) REFERENCES kindergarten (id)');
        $this->addSql('ALTER TABLE kindergarten ADD CONSTRAINT FK_B53C375B7E3C61F9 FOREIGN KEY (owner_id) REFERENCES owner (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE club DROP FOREIGN KEY FK_B8EE3872B9C0D867');
        $this->addSql('DROP TABLE club');
        $this->addSql('DROP TABLE kindergarten');
    }
}
