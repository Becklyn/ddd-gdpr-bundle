<?php

declare(strict_types=1);

namespace Becklyn\Ddd\PersonalData\Infrastructure\DoctrineMigrations;

use Doctrine\DBAL\Platforms\OraclePlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @author Marko Vujnovic <mv@becklyn.com>
 *
 * @since  2022-04-08
 */
final class Version20220408113020 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Creates the personal_data_store table';
    }

    public function up(Schema $schema) : void
    {
        if ($this->platform instanceof OraclePlatform) {
            $this->addSql('CREATE TABLE personal_data_store (id VARCHAR2(36) NOT NULL, aggregate_id VARCHAR2(36) NOT NULL, personal_value VARCHAR2(255) DEFAULT NULL NULL, days_to_live NUMBER(10) NOT NULL, event_raised_ts TIMESTAMP(6) NOT NULL, expires_ts TIMESTAMP(6) NOT NULL, anonymized_ts TIMESTAMP(6) DEFAULT NULL NULL, created_ts TIMESTAMP(6) NOT NULL, PRIMARY KEY(id))');
        } else {
            $this->addSql('CREATE TABLE personal_data_store (id VARCHAR(36) NOT NULL, aggregate_id VARCHAR(36) NOT NULL, personal_value VARCHAR(255) DEFAULT NULL, days_to_live INT NOT NULL, event_raised_ts DATETIME(6) NOT NULL, expires_ts DATETIME(6) NOT NULL, anonymized_ts DATETIME(6) DEFAULT NULL, created_ts DATETIME(6) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        }
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE personal_data_store');
    }
}
