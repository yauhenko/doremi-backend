<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221115142825 extends AbstractMigration {

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE user ADD role VARCHAR(20) NOT NULL, DROP roles');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL, DROP role');
    }

    public function isTransactional(): bool {
        return false;
    }

}
