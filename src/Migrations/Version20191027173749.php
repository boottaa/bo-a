<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191027173749 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE img (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL, size INT DEFAULT NULL, path VARCHAR(2000) DEFAULT NULL, url VARCHAR(2000) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE news ADD img_id INT DEFAULT NULL, DROP img');
        $this->addSql('ALTER TABLE news ADD CONSTRAINT FK_1DD39950C06A9F55 FOREIGN KEY (img_id) REFERENCES img (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1DD39950C06A9F55 ON news (img_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE news DROP FOREIGN KEY FK_1DD39950C06A9F55');
        $this->addSql('DROP TABLE img');
        $this->addSql('DROP INDEX UNIQ_1DD39950C06A9F55 ON news');
        $this->addSql('ALTER TABLE news ADD img VARCHAR(2000) NOT NULL COLLATE utf8mb4_unicode_ci, DROP img_id');
    }
}
