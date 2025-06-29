<?php

declare(strict_types=1);

// phpcs:ignoreFile
namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250104171839 extends AbstractMigration
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getDescription(): string
    {
        return 'Added waited_movie_id to user_waited_movie';
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform,
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE user_waited_movie ADD waited_movie_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_waited_movie ADD CONSTRAINT FK_7B7A5FF4315E506C FOREIGN KEY (waited_movie_id_id) REFERENCES waited_movie (id)');
        $this->addSql('CREATE INDEX IDX_7B7A5FF4315E506C ON user_waited_movie (waited_movie_id_id)');
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     *
     * {@inheritdoc}
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform,
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE user_waited_movie DROP FOREIGN KEY FK_7B7A5FF4315E506C');
        $this->addSql('DROP INDEX IDX_7B7A5FF4315E506C ON user_waited_movie');
        $this->addSql('ALTER TABLE user_waited_movie DROP waited_movie_id_id');
    }
}
