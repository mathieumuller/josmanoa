<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180105171046 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE album (id INT AUTO_INCREMENT NOT NULL, media_id INT DEFAULT NULL, owner_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_39986E43EA9FDD75 (media_id), INDEX IDX_39986E437E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media_album (album_id INT NOT NULL, media_id INT NOT NULL, INDEX IDX_2681AAF21137ABCF (album_id), INDEX IDX_2681AAF2EA9FDD75 (media_id), PRIMARY KEY(album_id, media_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, coordinates LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', date DATE DEFAULT NULL, date_from DATE DEFAULT NULL, date_to DATE DEFAULT NULL, upload_id VARCHAR(255) NOT NULL, status INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, file_name VARCHAR(255) DEFAULT NULL, file_original_name VARCHAR(255) DEFAULT NULL, file_mime_type VARCHAR(255) DEFAULT NULL, file_size INT DEFAULT NULL, file_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', INDEX IDX_6A2CA10C7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE thumbnail (id INT AUTO_INCREMENT NOT NULL, media_id INT DEFAULT NULL, path VARCHAR(255) NOT NULL, size VARCHAR(255) NOT NULL, INDEX IDX_C35726E6EA9FDD75 (media_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(80) NOT NULL, password VARCHAR(64) NOT NULL, email VARCHAR(80) NOT NULL, is_active TINYINT(1) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E43EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E437E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE media_album ADD CONSTRAINT FK_2681AAF21137ABCF FOREIGN KEY (album_id) REFERENCES album (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE media_album ADD CONSTRAINT FK_2681AAF2EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C7E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE thumbnail ADD CONSTRAINT FK_C35726E6EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE media_album DROP FOREIGN KEY FK_2681AAF21137ABCF');
        $this->addSql('ALTER TABLE album DROP FOREIGN KEY FK_39986E43EA9FDD75');
        $this->addSql('ALTER TABLE media_album DROP FOREIGN KEY FK_2681AAF2EA9FDD75');
        $this->addSql('ALTER TABLE thumbnail DROP FOREIGN KEY FK_C35726E6EA9FDD75');
        $this->addSql('ALTER TABLE album DROP FOREIGN KEY FK_39986E437E3C61F9');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C7E3C61F9');
        $this->addSql('DROP TABLE album');
        $this->addSql('DROP TABLE media_album');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE thumbnail');
        $this->addSql('DROP TABLE users');
    }
}
