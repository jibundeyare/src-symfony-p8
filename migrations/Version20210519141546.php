<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210519141546 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE project_client (project_id INT NOT NULL, client_id INT NOT NULL, INDEX IDX_D0E0EF1F166D1F9C (project_id), INDEX IDX_D0E0EF1F19EB6921 (client_id), PRIMARY KEY(project_id, client_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_tag (project_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_91F26D60166D1F9C (project_id), INDEX IDX_91F26D60BAD26311 (tag_id), PRIMARY KEY(project_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE project_client ADD CONSTRAINT FK_D0E0EF1F166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_client ADD CONSTRAINT FK_D0E0EF1F19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_tag ADD CONSTRAINT FK_91F26D60166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_tag ADD CONSTRAINT FK_91F26D60BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project ADD teacher_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE41807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE41807E1D ON project (teacher_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE project_client');
        $this->addSql('DROP TABLE project_tag');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE41807E1D');
        $this->addSql('DROP INDEX IDX_2FB3D0EE41807E1D ON project');
        $this->addSql('ALTER TABLE project DROP teacher_id');
    }
}
