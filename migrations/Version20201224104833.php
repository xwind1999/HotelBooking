<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201224104833 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE booking_room (id INT AUTO_INCREMENT NOT NULL, booking_id_id INT NOT NULL, room_id_id INT NOT NULL, number INT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, INDEX IDX_6A0E73D5EE3863E2 (booking_id_id), INDEX IDX_6A0E73D535F83FFC (room_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE booking_room ADD CONSTRAINT FK_6A0E73D5EE3863E2 FOREIGN KEY (booking_id_id) REFERENCES booking (id)');
        $this->addSql('ALTER TABLE booking_room ADD CONSTRAINT FK_6A0E73D535F83FFC FOREIGN KEY (room_id_id) REFERENCES room (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE booking_room');
    }
}
