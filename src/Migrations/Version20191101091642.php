<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191101091642 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE client CHANGE siret siret VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE materiel CHANGE prix_unitaire prix_unitaire DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE produit CHANGE reference reference VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE adresses_client DROP FOREIGN KEY FK_3EB02B50EA67784A');
        $this->addSql('DROP INDEX UNIQ_3EB02B50EA67784A ON adresses_client');
        $this->addSql('ALTER TABLE adresses_client DROP agenda_id');
        $this->addSql('ALTER TABLE devis CHANGE client_id client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE adresses_client ADD agenda_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE adresses_client ADD CONSTRAINT FK_3EB02B50EA67784A FOREIGN KEY (agenda_id) REFERENCES agenda (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3EB02B50EA67784A ON adresses_client (agenda_id)');
        $this->addSql('ALTER TABLE client CHANGE siret siret VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE devis CHANGE client_id client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE materiel CHANGE prix_unitaire prix_unitaire DOUBLE PRECISION DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE produit CHANGE reference reference VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
    }
}
