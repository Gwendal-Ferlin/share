<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241110131118 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fichier_user (fichier_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_C1C1EA2F915CFE (fichier_id), INDEX IDX_C1C1EA2A76ED395 (user_id), PRIMARY KEY(fichier_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fichier_user ADD CONSTRAINT FK_C1C1EA2F915CFE FOREIGN KEY (fichier_id) REFERENCES fichier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fichier_user ADD CONSTRAINT FK_C1C1EA2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fichier_user DROP FOREIGN KEY FK_C1C1EA2F915CFE');
        $this->addSql('ALTER TABLE fichier_user DROP FOREIGN KEY FK_C1C1EA2A76ED395');
        $this->addSql('DROP TABLE fichier_user');
    }
}
