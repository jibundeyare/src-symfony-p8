<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210519135354 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE student_tag (student_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_95F4B225CB944F1A (student_id), INDEX IDX_95F4B225BAD26311 (tag_id), PRIMARY KEY(student_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE student_tag ADD CONSTRAINT FK_95F4B225CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_tag ADD CONSTRAINT FK_95F4B225BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B723AF33A76ED395 ON student (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE student_tag');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF33A76ED395');
        $this->addSql('DROP INDEX UNIQ_B723AF33A76ED395 ON student');
        $this->addSql('ALTER TABLE student DROP user_id');
    }
}
