<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240518215639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clave DROP usado, CHANGE codigo codigo VARCHAR(20) NOT NULL');
        $this->addSql('ALTER TABLE usuario CHANGE saldo saldo NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE videojuego CHANGE url_foto url_foto VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clave ADD usado TINYINT(1) NOT NULL, CHANGE codigo codigo VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE usuario CHANGE saldo saldo NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE videojuego CHANGE url_foto url_foto VARCHAR(255) DEFAULT NULL');
    }
}
