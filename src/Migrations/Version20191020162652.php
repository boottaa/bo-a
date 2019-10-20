<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191020162652 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users_logs DROP FOREIGN KEY FK_3F06885267B3B43D');
        $this->addSql('DROP INDEX IDX_3F06885267B3B43D ON users_logs');
        $this->addSql('ALTER TABLE users_logs ADD status INT NOT NULL, CHANGE users_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE users_logs ADD CONSTRAINT FK_3F068852A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_3F068852A76ED395 ON users_logs (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users_logs DROP FOREIGN KEY FK_3F068852A76ED395');
        $this->addSql('DROP INDEX IDX_3F068852A76ED395 ON users_logs');
        $this->addSql('ALTER TABLE users_logs ADD users_id INT NOT NULL, DROP user_id, DROP status');
        $this->addSql('ALTER TABLE users_logs ADD CONSTRAINT FK_3F06885267B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_3F06885267B3B43D ON users_logs (users_id)');
    }
}
