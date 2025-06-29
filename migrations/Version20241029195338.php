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
final class Version20241029195338 extends AbstractMigration
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getDescription(): string
    {
        return 'Create User, FavoriteMovie, UserFavoriteMovie entities';
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

        $this->addSql('CREATE TABLE favorite_movie (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_favorite_movie (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, movie_favorite_id_id INT NOT NULL, INDEX IDX_C05306129D86650F (user_id_id), INDEX IDX_C0530612EAAA215B (movie_favorite_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_favorite_movie ADD CONSTRAINT FK_C05306129D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_favorite_movie ADD CONSTRAINT FK_C0530612EAAA215B FOREIGN KEY (movie_favorite_id_id) REFERENCES favorite_movie (id)');
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

        $this->addSql('ALTER TABLE user_favorite_movie DROP FOREIGN KEY FK_C05306129D86650F');
        $this->addSql('ALTER TABLE user_favorite_movie DROP FOREIGN KEY FK_C0530612EAAA215B');
        $this->addSql('DROP TABLE favorite_movie');
        $this->addSql('DROP TABLE scheduled_command');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_favorite_movie');
    }
}
