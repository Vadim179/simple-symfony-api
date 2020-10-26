<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201018180225 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE company_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE employee_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE role_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE company (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE employee (id INT NOT NULL, company_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5D9F75A1979B1AD6 ON employee (company_id)');
        $this->addSql('CREATE TABLE employee_role (employee_id INT NOT NULL, role_id INT NOT NULL, PRIMARY KEY(employee_id, role_id))');
        $this->addSql('CREATE INDEX IDX_E2B0C02D8C03F15C ON employee_role (employee_id)');
        $this->addSql('CREATE INDEX IDX_E2B0C02DD60322AC ON employee_role (role_id)');
        $this->addSql('CREATE TABLE role (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE employee_role ADD CONSTRAINT FK_E2B0C02D8C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE employee_role ADD CONSTRAINT FK_E2B0C02DD60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE employee DROP CONSTRAINT FK_5D9F75A1979B1AD6');
        $this->addSql('ALTER TABLE employee_role DROP CONSTRAINT FK_E2B0C02D8C03F15C');
        $this->addSql('ALTER TABLE employee_role DROP CONSTRAINT FK_E2B0C02DD60322AC');
        $this->addSql('DROP SEQUENCE company_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE employee_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE role_id_seq CASCADE');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE employee');
        $this->addSql('DROP TABLE employee_role');
        $this->addSql('DROP TABLE role');
    }
}
