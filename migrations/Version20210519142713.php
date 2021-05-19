<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210519142713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE teacher_tag (teacher_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_B1A33B741807E1D (teacher_id), INDEX IDX_B1A33B7BAD26311 (tag_id), PRIMARY KEY(teacher_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE teacher_tag ADD CONSTRAINT FK_B1A33B741807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE teacher_tag ADD CONSTRAINT FK_B1A33B7BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE teacher ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE teacher ADD CONSTRAINT FK_B0F6A6D5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B0F6A6D5A76ED395 ON teacher (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE teacher_tag');
        $this->addSql('ALTER TABLE teacher DROP FOREIGN KEY FK_B0F6A6D5A76ED395');
        $this->addSql('DROP INDEX UNIQ_B0F6A6D5A76ED395 ON teacher');
        $this->addSql('ALTER TABLE teacher DROP user_id');
    }
}
