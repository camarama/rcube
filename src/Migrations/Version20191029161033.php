<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191029161033 extends AbstractMigration
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
        $this->addSql('ALTER TABLE agenda DROP FOREIGN KEY FK_2CEDC8776AB213CC');
        $this->addSql('DROP INDEX IDX_2CEDC8776AB213CC ON agenda');
        $this->addSql('ALTER TABLE agenda DROP lieu_id');
        $this->addSql('ALTER TABLE materiel CHANGE prix_unitaire prix_unitaire DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE produit CHANGE reference reference VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE adresses_client ADD agenda_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE adresses_client ADD CONSTRAINT FK_3EB02B50EA67784A FOREIGN KEY (agenda_id) REFERENCES agenda (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3EB02B50EA67784A ON adresses_client (agenda_id)');
        $this->addSql('ALTER TABLE devis CHANGE client_id client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE adresses_client DROP FOREIGN KEY FK_3EB02B50EA67784A');
        $this->addSql('DROP INDEX UNIQ_3EB02B50EA67784A ON adresses_client');
        $this->addSql('ALTER TABLE adresses_client DROP agenda_id');
        $this->addSql('ALTER TABLE agenda ADD lieu_id INT NOT NULL');
        $this->addSql('ALTER TABLE agenda ADD CONSTRAINT FK_2CEDC8776AB213CC FOREIGN KEY (lieu_id) REFERENCES adresses_client (id)');
        $this->addSql('CREATE INDEX IDX_2CEDC8776AB213CC ON agenda (lieu_id)');
        $this->addSql('ALTER TABLE client CHANGE siret siret VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE devis CHANGE client_id client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE materiel CHANGE prix_unitaire prix_unitaire DOUBLE PRECISION DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE produit CHANGE reference reference VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
    }
}
