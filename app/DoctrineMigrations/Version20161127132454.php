<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161127132454 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, xml_title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, path VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, parent_category INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_fields (category_id INT NOT NULL, extra_fields_id INT NOT NULL, INDEX IDX_710D29F312469DE2 (category_id), INDEX IDX_710D29F3DE78E5B8 (extra_fields_id), PRIMARY KEY(category_id, extra_fields_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_treepath (tree_id INT AUTO_INCREMENT NOT NULL, ancestor INT NOT NULL, descendant INT NOT NULL, level INT NOT NULL, PRIMARY KEY(tree_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE extra_fields (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, xml_title VARCHAR(255) NOT NULL, type ENUM(\'number\', \'text\', \'date\'), show_it TINYINT(1) NOT NULL, showcase TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, good_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, is_image_update TINYINT(1) NOT NULL, name VARCHAR(255) NOT NULL, path VARCHAR(255) DEFAULT NULL, INDEX IDX_8C9F36101CF98C70 (good_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE good_extra_field (id INT AUTO_INCREMENT NOT NULL, good_id INT DEFAULT NULL, extra_field_id INT DEFAULT NULL, value VARCHAR(255) DEFAULT NULL, value_text VARCHAR(255) DEFAULT NULL, value_number NUMERIC(10, 2) DEFAULT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_AEB042A71CF98C70 (good_id), INDEX IDX_AEB042A7AEB5FE3 (extra_field_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE goods (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, short_title VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, xml_title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, brand VARCHAR(255) NOT NULL, article VARCHAR(255) NOT NULL, rating VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, slug VARCHAR(255) NOT NULL, category_cache VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_563B92D989D9B62 (slug), INDEX IDX_563B92D12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fos_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_957A647992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_957A6479A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_957A6479C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category_fields ADD CONSTRAINT FK_710D29F312469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_fields ADD CONSTRAINT FK_710D29F3DE78E5B8 FOREIGN KEY (extra_fields_id) REFERENCES extra_fields (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F36101CF98C70 FOREIGN KEY (good_id) REFERENCES goods (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE good_extra_field ADD CONSTRAINT FK_AEB042A71CF98C70 FOREIGN KEY (good_id) REFERENCES goods (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE good_extra_field ADD CONSTRAINT FK_AEB042A7AEB5FE3 FOREIGN KEY (extra_field_id) REFERENCES extra_fields (id)');
        $this->addSql('ALTER TABLE goods ADD CONSTRAINT FK_563B92D12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category_fields DROP FOREIGN KEY FK_710D29F312469DE2');
        $this->addSql('ALTER TABLE goods DROP FOREIGN KEY FK_563B92D12469DE2');
        $this->addSql('ALTER TABLE category_fields DROP FOREIGN KEY FK_710D29F3DE78E5B8');
        $this->addSql('ALTER TABLE good_extra_field DROP FOREIGN KEY FK_AEB042A7AEB5FE3');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F36101CF98C70');
        $this->addSql('ALTER TABLE good_extra_field DROP FOREIGN KEY FK_AEB042A71CF98C70');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE category_fields');
        $this->addSql('DROP TABLE category_treepath');
        $this->addSql('DROP TABLE extra_fields');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE good_extra_field');
        $this->addSql('DROP TABLE goods');
        $this->addSql('DROP TABLE fos_user');
    }
}
