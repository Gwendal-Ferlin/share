<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250507092253 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(30) NOT NULL, sujet VARCHAR(100) NOT NULL, email VARCHAR(100) NOT NULL, message LONGTEXT NOT NULL, date_envoi DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fichier (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, nom_original VARCHAR(255) NOT NULL, nom_serveur VARCHAR(255) NOT NULL, date_envoi DATETIME NOT NULL, extension VARCHAR(5) NOT NULL, taille INT NOT NULL, INDEX IDX_9B76551FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fichier_scategorie (fichier_id INT NOT NULL, scategorie_id INT NOT NULL, INDEX IDX_A1AB0BF6F915CFE (fichier_id), INDEX IDX_A1AB0BF6CF6A778A (scategorie_id), PRIMARY KEY(fichier_id, scategorie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fichier_user (fichier_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_C1C1EA2F915CFE (fichier_id), INDEX IDX_C1C1EA2A76ED395 (user_id), PRIMARY KEY(fichier_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scategorie (id INT AUTO_INCREMENT NOT NULL, categorie_id INT NOT NULL, libelle VARCHAR(50) NOT NULL, numero INT NOT NULL, INDEX IDX_51809477BCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, inscription_date DATETIME NOT NULL, name VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_demande (user_id INT NOT NULL, demander_id INT NOT NULL, INDEX IDX_7E99C9AFA76ED395 (user_id), INDEX IDX_7E99C9AF4C21AB48 (demander_id), PRIMARY KEY(user_id, demander_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_accepter (user_id INT NOT NULL, accepter_id INT NOT NULL, INDEX IDX_6244A71FA76ED395 (user_id), INDEX IDX_6244A71F48D7472D (accepter_id), PRIMARY KEY(user_id, accepter_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fichier ADD CONSTRAINT FK_9B76551FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE fichier_scategorie ADD CONSTRAINT FK_A1AB0BF6F915CFE FOREIGN KEY (fichier_id) REFERENCES fichier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fichier_scategorie ADD CONSTRAINT FK_A1AB0BF6CF6A778A FOREIGN KEY (scategorie_id) REFERENCES scategorie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fichier_user ADD CONSTRAINT FK_C1C1EA2F915CFE FOREIGN KEY (fichier_id) REFERENCES fichier (id)');
        $this->addSql('ALTER TABLE fichier_user ADD CONSTRAINT FK_C1C1EA2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE scategorie ADD CONSTRAINT FK_51809477BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE user_demande ADD CONSTRAINT FK_7E99C9AFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_demande ADD CONSTRAINT FK_7E99C9AF4C21AB48 FOREIGN KEY (demander_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_accepter ADD CONSTRAINT FK_6244A71FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_accepter ADD CONSTRAINT FK_6244A71F48D7472D FOREIGN KEY (accepter_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fichier DROP FOREIGN KEY FK_9B76551FA76ED395');
        $this->addSql('ALTER TABLE fichier_scategorie DROP FOREIGN KEY FK_A1AB0BF6F915CFE');
        $this->addSql('ALTER TABLE fichier_scategorie DROP FOREIGN KEY FK_A1AB0BF6CF6A778A');
        $this->addSql('ALTER TABLE fichier_user DROP FOREIGN KEY FK_C1C1EA2F915CFE');
        $this->addSql('ALTER TABLE fichier_user DROP FOREIGN KEY FK_C1C1EA2A76ED395');
        $this->addSql('ALTER TABLE scategorie DROP FOREIGN KEY FK_51809477BCF5E72D');
        $this->addSql('ALTER TABLE user_demande DROP FOREIGN KEY FK_7E99C9AFA76ED395');
        $this->addSql('ALTER TABLE user_demande DROP FOREIGN KEY FK_7E99C9AF4C21AB48');
        $this->addSql('ALTER TABLE user_accepter DROP FOREIGN KEY FK_6244A71FA76ED395');
        $this->addSql('ALTER TABLE user_accepter DROP FOREIGN KEY FK_6244A71F48D7472D');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE fichier');
        $this->addSql('DROP TABLE fichier_scategorie');
        $this->addSql('DROP TABLE fichier_user');
        $this->addSql('DROP TABLE scategorie');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_demande');
        $this->addSql('DROP TABLE user_accepter');
    }
}
