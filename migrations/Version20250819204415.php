<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250819204415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cuota (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, fecha_vencimiento DATE NOT NULL, valor DOUBLE PRECISION NOT NULL)');
        $this->addSql('CREATE TABLE dictado (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, fecha_inicio DATE NOT NULL, fecha_fin DATE NOT NULL)');
        $this->addSql('CREATE TABLE nota (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, valor INTEGER NOT NULL)');
        $this->addSql('CREATE TABLE pago (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, valor DOUBLE PRECISION NOT NULL)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE cuota');
        $this->addSql('DROP TABLE dictado');
        $this->addSql('DROP TABLE nota');
        $this->addSql('DROP TABLE pago');
    }
}
