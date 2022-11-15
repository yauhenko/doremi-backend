<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221115094644 extends AbstractMigration {

    public function up(Schema $schema): void {
        $this->addSql('CREATE TABLE upload (id CHAR(32) NOT NULL, path VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, mime VARCHAR(50) NOT NULL, size INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', touched_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_locked TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void {
        $this->addSql('DROP TABLE upload');
    }

    public function isTransactional(): bool {
        return false;
    }

}
