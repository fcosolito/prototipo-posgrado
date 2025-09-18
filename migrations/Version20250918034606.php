<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250918034606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE alumno (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, apellido VARCHAR(255) NOT NULL, correo VARCHAR(255) DEFAULT NULL, dni INTEGER NOT NULL, cuil INTEGER DEFAULT NULL, legajo VARCHAR(50) DEFAULT NULL)');
        $this->addSql('CREATE TABLE carrera (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE cuota (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, alumno_id INTEGER NOT NULL, fecha_vencimiento DATE NOT NULL, valor DOUBLE PRECISION NOT NULL, CONSTRAINT FK_763CCB0FFC28E5EE FOREIGN KEY (alumno_id) REFERENCES alumno (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_763CCB0FFC28E5EE ON cuota (alumno_id)');
        $this->addSql('CREATE TABLE curso (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, carrera_id INTEGER DEFAULT NULL, docente_id INTEGER DEFAULT NULL, nombre VARCHAR(255) NOT NULL, horas INTEGER NOT NULL, es_obligatorio BOOLEAN DEFAULT NULL, tarifa_mensual NUMERIC(10, 2) DEFAULT NULL, CONSTRAINT FK_CA3B40ECC671B40F FOREIGN KEY (carrera_id) REFERENCES carrera (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_CA3B40EC94E27525 FOREIGN KEY (docente_id) REFERENCES docente (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_CA3B40ECC671B40F ON curso (carrera_id)');
        $this->addSql('CREATE INDEX IDX_CA3B40EC94E27525 ON curso (docente_id)');
        $this->addSql('CREATE TABLE dictado (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, curso_id INTEGER NOT NULL, fecha_inicio DATE NOT NULL, fecha_fin DATE NOT NULL, nombre VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_6228A03187CB4A1F FOREIGN KEY (curso_id) REFERENCES curso (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_6228A03187CB4A1F ON dictado (curso_id)');
        $this->addSql('CREATE TABLE docente (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, apellido VARCHAR(255) NOT NULL, correo VARCHAR(255) DEFAULT NULL, dni INTEGER NOT NULL, especialidad VARCHAR(255) DEFAULT NULL, titulo VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE TABLE inscripcion (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, alumno_id INTEGER NOT NULL, dictado_id INTEGER NOT NULL, CONSTRAINT FK_935E99F0FC28E5EE FOREIGN KEY (alumno_id) REFERENCES alumno (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_935E99F0D85A5EE6 FOREIGN KEY (dictado_id) REFERENCES dictado (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_935E99F0FC28E5EE ON inscripcion (alumno_id)');
        $this->addSql('CREATE INDEX IDX_935E99F0D85A5EE6 ON inscripcion (dictado_id)');
        $this->addSql('CREATE TABLE legajo (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, alumno_id INTEGER NOT NULL, carrera_id INTEGER NOT NULL, numero VARCHAR(50) NOT NULL, fecha_inscripcion DATE DEFAULT NULL, fecha_egreso DATE DEFAULT NULL, estado VARCHAR(50) DEFAULT NULL, CONSTRAINT FK_32DD07F6FC28E5EE FOREIGN KEY (alumno_id) REFERENCES alumno (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_32DD07F6C671B40F FOREIGN KEY (carrera_id) REFERENCES carrera (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_32DD07F6FC28E5EE ON legajo (alumno_id)');
        $this->addSql('CREATE INDEX IDX_32DD07F6C671B40F ON legajo (carrera_id)');
        $this->addSql('CREATE TABLE nota (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, inscripcion_id INTEGER NOT NULL, valor INTEGER NOT NULL, CONSTRAINT FK_C8D03E0DFFD5FBD3 FOREIGN KEY (inscripcion_id) REFERENCES inscripcion (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C8D03E0DFFD5FBD3 ON nota (inscripcion_id)');
        $this->addSql('CREATE TABLE pago (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, cuota_id INTEGER NOT NULL, valor DOUBLE PRECISION NOT NULL, CONSTRAINT FK_F4DF5F3E6A7CF079 FOREIGN KEY (cuota_id) REFERENCES cuota (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F4DF5F3E6A7CF079 ON pago (cuota_id)');
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
        $this->addSql('DROP TABLE alumno');
        $this->addSql('DROP TABLE carrera');
        $this->addSql('DROP TABLE cuota');
        $this->addSql('DROP TABLE curso');
        $this->addSql('DROP TABLE dictado');
        $this->addSql('DROP TABLE docente');
        $this->addSql('DROP TABLE inscripcion');
        $this->addSql('DROP TABLE legajo');
        $this->addSql('DROP TABLE nota');
        $this->addSql('DROP TABLE pago');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
