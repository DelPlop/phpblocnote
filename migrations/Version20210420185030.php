<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210420185030 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE sfp_category (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, position SMALLINT NOT NULL, INDEX IDX_64C19C1A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sfp_note (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, visibility VARCHAR(20) NOT NULL, INDEX IDX_CFBDFA14A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sfp_note_category (id INT AUTO_INCREMENT NOT NULL, note_id INT NOT NULL, category_id INT NOT NULL, position SMALLINT NOT NULL DEFAULT 0, INDEX IDX_1617C55F26ED0855 (note_id), INDEX IDX_1617C55F12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sfp_category ADD CONSTRAINT FK_64C19C1A76ED395 FOREIGN KEY (user_id) REFERENCES sfp_user (id)');
        $this->addSql('ALTER TABLE sfp_note ADD CONSTRAINT FK_CFBDFA14A76ED395 FOREIGN KEY (user_id) REFERENCES sfp_user (id)');
        $this->addSql('ALTER TABLE sfp_note_category ADD CONSTRAINT FK_1617C55F26ED0855 FOREIGN KEY (note_id) REFERENCES sfp_note (id)');
        $this->addSql('ALTER TABLE sfp_note_category ADD CONSTRAINT FK_1617C55F12469DE2 FOREIGN KEY (category_id) REFERENCES sfp_category (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE sfp_category');
        $this->addSql('DROP TABLE sfp_note');
        $this->addSql('DROP TABLE sfp_note_category');
    }
}
