<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250901150031 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE client (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client_nom VARCHAR(255) NOT NULL, client_adresse CLOB DEFAULT NULL)');
        $this->addSql('CREATE TABLE facture (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client_id INTEGER NOT NULL, ref_id VARCHAR(50) NOT NULL, date_facturation DATE NOT NULL, date_echeance DATE NOT NULL, total_ht NUMERIC(12, 2) NOT NULL, total_ttc NUMERIC(12, 2) NOT NULL, CONSTRAINT FK_FE86641019EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_FE86641019EB6921 ON facture (client_id)');
        $this->addSql('CREATE TABLE pro_facture (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, facture_id INTEGER NOT NULL, produit_id INTEGER NOT NULL, tva_id INTEGER NOT NULL, quantite_produit INTEGER NOT NULL, tarifs_ht_produit NUMERIC(12, 2) NOT NULL, CONSTRAINT FK_B2FA2CB7F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_B2FA2CBF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_B2FA2CB4D79775F FOREIGN KEY (tva_id) REFERENCES tva (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_B2FA2CB7F2DEE08 ON pro_facture (facture_id)');
        $this->addSql('CREATE INDEX IDX_B2FA2CBF347EFB ON pro_facture (produit_id)');
        $this->addSql('CREATE INDEX IDX_B2FA2CB4D79775F ON pro_facture (tva_id)');
        $this->addSql('CREATE TABLE produit (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, tva_id INTEGER NOT NULL, tarifs_ht_produit NUMERIC(12, 2) NOT NULL, CONSTRAINT FK_29A5EC274D79775F FOREIGN KEY (tva_id) REFERENCES tva (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_29A5EC274D79775F ON produit (tva_id)');
        $this->addSql('CREATE TABLE tva (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, date_debut_tva DATE NOT NULL, date_fin_tva DATE DEFAULT NULL, taux NUMERIC(5, 3) NOT NULL)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE facture');
        $this->addSql('DROP TABLE pro_facture');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE tva');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
