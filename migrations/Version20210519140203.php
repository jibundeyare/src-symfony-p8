<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210519140203 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE school_year_teacher (school_year_id INT NOT NULL, teacher_id INT NOT NULL, INDEX IDX_696BEE12D2EECC3F (school_year_id), INDEX IDX_696BEE1241807E1D (teacher_id), PRIMARY KEY(school_year_id, teacher_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE school_year_teacher ADD CONSTRAINT FK_696BEE12D2EECC3F FOREIGN KEY (school_year_id) REFERENCES school_year (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE school_year_teacher ADD CONSTRAINT FK_696BEE1241807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE school_year_teacher');
    }
}
