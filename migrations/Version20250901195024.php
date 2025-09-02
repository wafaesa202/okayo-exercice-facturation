<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250901195024 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pro_facture ADD COLUMN designation_produit VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE pro_facture ADD COLUMN taux_tva_applique NUMERIC(5, 2) NOT NULL');
        $this->addSql('CREATE TEMPORARY TABLE __temp__tva AS SELECT id, date_debut_tva, date_fin_tva, taux FROM tva');
        $this->addSql('DROP TABLE tva');
        $this->addSql('CREATE TABLE tva (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, dateDebut_tva DATE NOT NULL, dateFin_tva DATE DEFAULT NULL, taux NUMERIC(5, 3) NOT NULL)');
        $this->addSql('INSERT INTO tva (id, dateDebut_tva, dateFin_tva, taux) SELECT id, date_debut_tva, date_fin_tva, taux FROM __temp__tva');
        $this->addSql('DROP TABLE __temp__tva');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__pro_facture AS SELECT id, facture_id, produit_id, tva_id, quantite_produit, tarifs_ht_produit FROM pro_facture');
        $this->addSql('DROP TABLE pro_facture');
        $this->addSql('CREATE TABLE pro_facture (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, facture_id INTEGER NOT NULL, produit_id INTEGER NOT NULL, tva_id INTEGER NOT NULL, quantite_produit INTEGER NOT NULL, tarifs_ht_produit NUMERIC(12, 2) NOT NULL, CONSTRAINT FK_B2FA2CB7F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_B2FA2CBF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_B2FA2CB4D79775F FOREIGN KEY (tva_id) REFERENCES tva (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO pro_facture (id, facture_id, produit_id, tva_id, quantite_produit, tarifs_ht_produit) SELECT id, facture_id, produit_id, tva_id, quantite_produit, tarifs_ht_produit FROM __temp__pro_facture');
        $this->addSql('DROP TABLE __temp__pro_facture');
        $this->addSql('CREATE INDEX IDX_B2FA2CB7F2DEE08 ON pro_facture (facture_id)');
        $this->addSql('CREATE INDEX IDX_B2FA2CBF347EFB ON pro_facture (produit_id)');
        $this->addSql('CREATE INDEX IDX_B2FA2CB4D79775F ON pro_facture (tva_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__tva AS SELECT id, dateDebut_tva, dateFin_tva, taux FROM tva');
        $this->addSql('DROP TABLE tva');
        $this->addSql('CREATE TABLE tva (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, date_debut_tva DATE NOT NULL, date_fin_tva DATE DEFAULT NULL, taux NUMERIC(5, 3) NOT NULL)');
        $this->addSql('INSERT INTO tva (id, date_debut_tva, date_fin_tva, taux) SELECT id, dateDebut_tva, dateFin_tva, taux FROM __temp__tva');
        $this->addSql('DROP TABLE __temp__tva');
    }
}
