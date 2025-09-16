<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250908195536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dictado ADD COLUMN nombre VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__dictado AS SELECT id, curso_id, fecha_inicio, fecha_fin FROM dictado');
        $this->addSql('DROP TABLE dictado');
        $this->addSql('CREATE TABLE dictado (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, curso_id INTEGER NOT NULL, fecha_inicio DATE NOT NULL, fecha_fin DATE NOT NULL, CONSTRAINT FK_6228A03187CB4A1F FOREIGN KEY (curso_id) REFERENCES curso (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO dictado (id, curso_id, fecha_inicio, fecha_fin) SELECT id, curso_id, fecha_inicio, fecha_fin FROM __temp__dictado');
        $this->addSql('DROP TABLE __temp__dictado');
        $this->addSql('CREATE INDEX IDX_6228A03187CB4A1F ON dictado (curso_id)');
    }
}
