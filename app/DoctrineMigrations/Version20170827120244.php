<?php

namespace Prooph\ProophessorDo\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Prooph\ProophessorDo\Projection\Table;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170827120244 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $todoTable = $schema->getTable(Table::TODO);
        $todoTable->addColumn('reminded', 'boolean', ['default' => false, 'notnull' => false]);

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $todoTable = $schema->getTable(Table::TODO);
        $todoTable->dropColumn('reminded');
    }
}
