<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191027185520 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE news DROP FOREIGN KEY FK_1DD39950C06A9F55');
        $this->addSql('ALTER TABLE news CHANGE img_id img_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE news ADD CONSTRAINT FK_1DD39950C06A9F55 FOREIGN KEY (img_id) REFERENCES img (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE img CHANGE size size INT DEFAULT NULL, CHANGE path path VARCHAR(2000) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE img CHANGE size size INT DEFAULT NULL, CHANGE path path VARCHAR(2000) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE news DROP FOREIGN KEY FK_1DD39950C06A9F55');
        $this->addSql('ALTER TABLE news CHANGE img_id img_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE news ADD CONSTRAINT FK_1DD39950C06A9F55 FOREIGN KEY (img_id) REFERENCES img (id)');
    }
}
