<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260213101026 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create phoenix_access_tokens table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE phoenix_access_tokens (
            id SERIAL PRIMARY KEY,
            user_id INTEGER NOT NULL,
            access_token VARCHAR(255) NOT NULL,
            created_at TIMESTAMP NOT NULL,
            CONSTRAINT fk_phoenix_access_tokens_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )');

        $this->addSql('CREATE INDEX idx_phoenix_access_tokens_user_id ON phoenix_access_tokens(user_id)');
        $this->addSql('CREATE INDEX idx_phoenix_access_tokens_token ON phoenix_access_tokens(access_token)');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE phoenix_access_tokens');
    }
}
