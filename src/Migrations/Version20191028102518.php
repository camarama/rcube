<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191028102518 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE agenda (id INT AUTO_INCREMENT NOT NULL, lieu_id INT NOT NULL, rendez_vous DATETIME NOT NULL, designation VARCHAR(255) NOT NULL, note LONGTEXT NOT NULL, INDEX IDX_2CEDC8776AB213CC (lieu_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE agenda ADD CONSTRAINT FK_2CEDC8776AB213CC FOREIGN KEY (lieu_id) REFERENCES adresses_client (id)');
        $this->addSql('ALTER TABLE client CHANGE siret siret VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE materiel CHANGE prix_unitaire prix_unitaire DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE produit CHANGE reference reference VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE devis CHANGE client_id client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE agenda');
        $this->addSql('ALTER TABLE client CHANGE siret siret VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE devis CHANGE client_id client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE materiel CHANGE prix_unitaire prix_unitaire DOUBLE PRECISION DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE produit CHANGE reference reference VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
    }
}
