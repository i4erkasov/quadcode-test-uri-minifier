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

        $this->addSql("CREATE OR REPLACE FUNCTION base36_encode(IN digits bigint, IN min_width int = 0)
                                RETURNS varchar AS $$
                                    DECLARE
			                            chars char[];
			                            ret varchar;
			                            val bigint;
		                            BEGIN
		                            chars := ARRAY['0','1','2','3','4','5','6','7','8','9'
			                                        ,'a','b','c','d','e','f','g','h','i','j','k','l','m'
			                                        ,'n','o','p','q','r','s','t','u','v','w','x','y','z'];
		                            val := digits;
		                            ret := '';
		                            IF val < 0 THEN
			                            val := val * -1;
		                            END IF;
		                            
		                            WHILE val != 0 LOOP
			                            ret := chars[(val % 36)+1] || ret;
                                        val := val / 36;
                                    END LOOP;

                                    IF min_width > 0 AND char_length(ret) < min_width THEN
                                        ret := lpad(ret, min_width, '0');
                                    END IF;

		                            RETURN ret;
 
                            END;
                            $$ LANGUAGE 'plpgsql' IMMUTABLE;");

        $this->addSql("CREATE OR REPLACE FUNCTION base36_decode(IN base36 varchar)
                              RETURNS bigint AS $$
                                    DECLARE
                                        a char[];
                                        ret bigint;
                                        i int;
                                        val int;
                                        chars varchar;
                                    BEGIN
                                    chars := '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                             
                                    FOR i IN REVERSE char_length(base36)..1 LOOP
                                        a := a || substring(upper(base36) FROM i FOR 1)::char;
                                    END LOOP;
                                    i := 0;
                                    ret := 0;
                                    WHILE i < (array_length(a,1)) LOOP		
                                        val := position(a[i+1] IN chars)-1;
                                        ret := ret + (val * (36 ^ i))::bigint;
                                        i := i + 1;
                                    END LOOP;
                             
                                    RETURN ret;
                             
                            END;
                            $$ LANGUAGE 'plpgsql' IMMUTABLE;");
    }

    public function down(Schema $schema): void
    {
        // echo  $a = base_convert(100, 10, 36);
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE links_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE statistic_redirect_id_seq CASCADE');
        $this->addSql('DROP TABLE short_urls');
        $this->addSql('DROP TABLE statistic_redirect');
        $this->addSql('DROP FUNCTION base36_encode');
        $this->addSql('DROP FUNCTION base36_decode');
    }
}
