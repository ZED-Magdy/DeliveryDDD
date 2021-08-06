<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210806144704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE offers (id VARCHAR(255) NOT NULL, driver_id VARCHAR(255) DEFAULT NULL, order_id VARCHAR(255) NOT NULL, price VARCHAR(255) NOT NULL, accepted_at DATE DEFAULT NULL --(DC2Type:date_immutable)
        , PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DA460427C3423909 ON offers (driver_id)');
        $this->addSql('CREATE INDEX IDX_DA4604278D9F6D38 ON offers (order_id)');
        $this->addSql('CREATE TABLE orders (id VARCHAR(255) NOT NULL, accepted_offer_id VARCHAR(255) DEFAULT NULL, owner_id VARCHAR(255) DEFAULT NULL, driver_id VARCHAR(255) DEFAULT NULL, price VARCHAR(255) DEFAULT NULL, status SMALLINT NOT NULL, note VARCHAR(255) DEFAULT NULL, driver_arrived_at DATE DEFAULT NULL --(DC2Type:date_immutable)
        , finished_at DATE DEFAULT NULL --(DC2Type:date_immutable)
        , offer_accepted_at DATE DEFAULT NULL --(DC2Type:date_immutable)
        , published_at DATE DEFAULT NULL --(DC2Type:date_immutable)
        , order_place PLACE NOT NULL, drop_place PLACE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E52FFDEE8920E704 ON orders (accepted_offer_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE7E3C61F9 ON orders (owner_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEEC3423909 ON orders (driver_id)');
        $this->addSql('CREATE TABLE products (id VARCHAR(255) NOT NULL, order_id VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, quantity VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B3BA5A5A8D9F6D38 ON products (order_id)');
        $this->addSql('CREATE TABLE security_users (id VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles CLOB NOT NULL --(DC2Type:json_array)
        , PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F83F4643E7927C74 ON security_users (email)');
        $this->addSql('CREATE TABLE users (id VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, status SMALLINT NOT NULL, user_type VARCHAR(255) NOT NULL, fees VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE offers');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE security_users');
        $this->addSql('DROP TABLE users');
    }
}
