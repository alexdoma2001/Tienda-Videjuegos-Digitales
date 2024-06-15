<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240421093850 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE clave (id INT AUTO_INCREMENT NOT NULL, videojuego_id INT NOT NULL, pedido_id INT DEFAULT NULL, codigo VARCHAR(255) NOT NULL, INDEX IDX_64E8588B82925A85 (videojuego_id), INDEX IDX_64E8588B4854653A (pedido_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE videojuego (id INT AUTO_INCREMENT NOT NULL, categoria_id INT NOT NULL, nombre VARCHAR(255) NOT NULL, descripcion LONGTEXT DEFAULT NULL, url_foto VARCHAR(255) DEFAULT NULL, precio NUMERIC(10, 2) NOT NULL, INDEX IDX_AA5E6DFA3397707A (categoria_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE clave ADD CONSTRAINT FK_64E8588B82925A85 FOREIGN KEY (videojuego_id) REFERENCES videojuego (id)');
        $this->addSql('ALTER TABLE clave ADD CONSTRAINT FK_64E8588B4854653A FOREIGN KEY (pedido_id) REFERENCES pedido (id)');
        $this->addSql('ALTER TABLE videojuego ADD CONSTRAINT FK_AA5E6DFA3397707A FOREIGN KEY (categoria_id) REFERENCES categoria (id)');
        $this->addSql('ALTER TABLE pedido_producto DROP FOREIGN KEY FK_DD333C24854653A');
        $this->addSql('ALTER TABLE pedido_producto DROP FOREIGN KEY FK_DD333C27645698E');
        $this->addSql('ALTER TABLE producto DROP FOREIGN KEY FK_A7BB06153397707A');
        $this->addSql('DROP TABLE pedido_producto');
        $this->addSql('DROP TABLE producto');
        $this->addSql('ALTER TABLE pedido CHANGE precio_final precio_final NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE usuario DROP saldo');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pedido_producto (pedido_id INT NOT NULL, producto_id INT NOT NULL, INDEX IDX_DD333C27645698E (producto_id), INDEX IDX_DD333C24854653A (pedido_id), PRIMARY KEY(pedido_id, producto_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE producto (id INT AUTO_INCREMENT NOT NULL, categoria_id INT DEFAULT NULL, nombre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, descripcion LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, precio NUMERIC(10, 0) NOT NULL, stock INT NOT NULL, url_foto VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_A7BB06153397707A (categoria_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE pedido_producto ADD CONSTRAINT FK_DD333C24854653A FOREIGN KEY (pedido_id) REFERENCES pedido (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pedido_producto ADD CONSTRAINT FK_DD333C27645698E FOREIGN KEY (producto_id) REFERENCES producto (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE producto ADD CONSTRAINT FK_A7BB06153397707A FOREIGN KEY (categoria_id) REFERENCES categoria (id)');
        $this->addSql('ALTER TABLE clave DROP FOREIGN KEY FK_64E8588B82925A85');
        $this->addSql('ALTER TABLE clave DROP FOREIGN KEY FK_64E8588B4854653A');
        $this->addSql('ALTER TABLE videojuego DROP FOREIGN KEY FK_AA5E6DFA3397707A');
        $this->addSql('DROP TABLE clave');
        $this->addSql('DROP TABLE videojuego');
        $this->addSql('ALTER TABLE pedido CHANGE precio_final precio_final NUMERIC(10, 0) NOT NULL');
        $this->addSql('ALTER TABLE usuario ADD saldo INT NOT NULL');
    }
}
