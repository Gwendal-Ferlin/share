<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241105103125 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_accepter (user_id INT NOT NULL, accepter_id INT NOT NULL, INDEX IDX_6244A71FA76ED395 (user_id), INDEX IDX_6244A71F48D7472D (accepter_id), PRIMARY KEY(user_id, accepter_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_accepter ADD CONSTRAINT FK_6244A71FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_accepter ADD CONSTRAINT FK_6244A71F48D7472D FOREIGN KEY (accepter_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_accepter DROP FOREIGN KEY FK_6244A71FA76ED395');
        $this->addSql('ALTER TABLE user_accepter DROP FOREIGN KEY FK_6244A71F48D7472D');
        $this->addSql('DROP TABLE user_accepter');
    }
}
