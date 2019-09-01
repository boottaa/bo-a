<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190824165128 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE followers (user_id INT NOT NULL, followe_user_id INT NOT NULL, INDEX IDX_8408FDA7A76ED395 (user_id), INDEX IDX_8408FDA7A3063E70 (followe_user_id), PRIMARY KEY(user_id, followe_user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE followers ADD CONSTRAINT FK_8408FDA7A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE followers ADD CONSTRAINT FK_8408FDA7A3063E70 FOREIGN KEY (followe_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users CHANGE f_name f_name VARCHAR(255) DEFAULT NULL, CHANGE l_name l_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE followers');
        $this->addSql('ALTER TABLE users CHANGE f_name f_name VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE l_name l_name VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
    }
}
