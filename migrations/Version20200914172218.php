<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200914172218 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS short_urls (
                                id BIGSERIAL NOT NULL,
                                url VARCHAR(2048) NOT NULL, 
                                code VARCHAR(10) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                                removed BOOLEAN NOT NULL DEFAULT false,
                           PRIMARY KEY(id))'
        );

        $this->addSql('CREATE UNIQUE INDEX UNIQ_D182A11877153098 ON short_urls (code)');
        $this->addSql('CREATE TABLE IF NOT EXISTS statistic_redirect (
                                id BIGSERIAL NOT NULL,
                                url_id INT NOT NULL, 
                                redirect_datetime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                           FOREIGN KEY (url_id) REFERENCES short_urls (id) ON DELETE CASCADE,
                           PRIMARY KEY(id))'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE links_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE statistic_redirect_id_seq CASCADE');
        $this->addSql('DROP TABLE short_urls');
        $this->addSql('DROP TABLE statistic_redirect');
        $this->addSql('DROP FUNCTION base36_encode');
        $this->addSql('DROP FUNCTION base36_decode');
    }
}
