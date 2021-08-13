<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210507153030 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE curve_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE lantern_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE lantern_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE locality_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE logs_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE map_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE street_class_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_account_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE curve (id INT NOT NULL, name VARCHAR(255) NOT NULL, coef_table TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE lantern (id INT NOT NULL, type_id INT NOT NULL, curve_id INT NOT NULL, name VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, light_flow INT NOT NULL, isolux TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_29928517C54C8C93 ON lantern (type_id)');
        $this->addSql('CREATE INDEX IDX_299285172096250B ON lantern (curve_id)');
        $this->addSql('CREATE TABLE lantern_type (id INT NOT NULL, name VARCHAR(255) NOT NULL, coef_table TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE locality (id INT NOT NULL, name VARCHAR(255) NOT NULL, data_movement VARCHAR(1000) DEFAULT NULL, latitude TEXT NOT NULL, longitude TEXT NOT NULL, timezone VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E1D6B8E65E237E06 ON locality (name)');
        $this->addSql('COMMENT ON COLUMN locality.latitude IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN locality.longitude IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE logs (id INT NOT NULL, map_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, description VARCHAR(5000) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F08FC65C53C55F64 ON logs (map_id)');
        $this->addSql('CREATE TABLE map (id INT NOT NULL, user_account_id INT NOT NULL, locality_id INT NOT NULL, data TEXT DEFAULT NULL, name VARCHAR(255) NOT NULL, report VARCHAR(1000) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_93ADAABB3C0C9956 ON map (user_account_id)');
        $this->addSql('CREATE INDEX IDX_93ADAABB88823A92 ON map (locality_id)');
        $this->addSql('CREATE TABLE map_lantern (map_id INT NOT NULL, lantern_id INT NOT NULL, PRIMARY KEY(map_id, lantern_id))');
        $this->addSql('CREATE INDEX IDX_6B97D4B53C55F64 ON map_lantern (map_id)');
        $this->addSql('CREATE INDEX IDX_6B97D4B27DDEE8A ON map_lantern (lantern_id)');
        $this->addSql('CREATE TABLE street_class (id INT NOT NULL, name VARCHAR(255) NOT NULL, average_illumination DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE user_account (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_253B48AEE7927C74 ON user_account (email)');
        $this->addSql('ALTER TABLE lantern ADD CONSTRAINT FK_29928517C54C8C93 FOREIGN KEY (type_id) REFERENCES lantern_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE lantern ADD CONSTRAINT FK_299285172096250B FOREIGN KEY (curve_id) REFERENCES curve (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE logs ADD CONSTRAINT FK_F08FC65C53C55F64 FOREIGN KEY (map_id) REFERENCES map (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE map ADD CONSTRAINT FK_93ADAABB3C0C9956 FOREIGN KEY (user_account_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE map ADD CONSTRAINT FK_93ADAABB88823A92 FOREIGN KEY (locality_id) REFERENCES locality (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE map_lantern ADD CONSTRAINT FK_6B97D4B53C55F64 FOREIGN KEY (map_id) REFERENCES map (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE map_lantern ADD CONSTRAINT FK_6B97D4B27DDEE8A FOREIGN KEY (lantern_id) REFERENCES lantern (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lantern DROP CONSTRAINT FK_299285172096250B');
        $this->addSql('ALTER TABLE map_lantern DROP CONSTRAINT FK_6B97D4B27DDEE8A');
        $this->addSql('ALTER TABLE lantern DROP CONSTRAINT FK_29928517C54C8C93');
        $this->addSql('ALTER TABLE map DROP CONSTRAINT FK_93ADAABB88823A92');
        $this->addSql('ALTER TABLE logs DROP CONSTRAINT FK_F08FC65C53C55F64');
        $this->addSql('ALTER TABLE map_lantern DROP CONSTRAINT FK_6B97D4B53C55F64');
        $this->addSql('ALTER TABLE map DROP CONSTRAINT FK_93ADAABB3C0C9956');
        $this->addSql('DROP SEQUENCE curve_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE lantern_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE lantern_type_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE locality_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE logs_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE map_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE street_class_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_account_id_seq CASCADE');
        $this->addSql('DROP TABLE curve');
        $this->addSql('DROP TABLE lantern');
        $this->addSql('DROP TABLE lantern_type');
        $this->addSql('DROP TABLE locality');
        $this->addSql('DROP TABLE logs');
        $this->addSql('DROP TABLE map');
        $this->addSql('DROP TABLE map_lantern');
        $this->addSql('DROP TABLE street_class');
        $this->addSql('DROP TABLE user_account');
    }
}
