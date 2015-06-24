<?php

namespace OroCRM\Bundle\IssueBundle\Migrations\Schema\v1_0;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class OroCRMIssueBundle implements Migration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        /* Tables generation **/
        $this->createOrocrmIssueTable($schema);

        /* Foreign keys generation **/
        $this->addOrocrmIssueForeignKeys($schema);
    }

    /**
     * @param Schema $schema
     */
    protected function createOrocrmIssueTable(Schema $schema)
    {
        $table = $schema->createTable('orocrm_issue');

        $table->addColumn('id', 'integer', ['autoincrement' => true]);

        $table->addColumn('code', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('summary', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('description', 'text', ['notnull' => false]);

        $table->addColumn('createdAt', 'datetime', []);
        $table->addColumn('updatedAt', 'datetime', []);

        $table->addColumn('owner_id', 'integer', ['notnull' => false]);
        $table->addColumn('reporter_id', 'integer', ['notnull' => false]);

        $table->setPrimaryKey(['id']);

        $table->addIndex(['owner_id'], 'IDX_EF1CE9717E3C61F9', []);
        $table->addIndex(['reporter_id'], 'IDX_EF1CE971E1CFE6F5', []);
    }

    /**
     * @param Schema $schema
     */
    protected function addOrocrmIssueForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('orocrm_issue');

        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['owner_id'],
            ['id'],
            ['onDelete' => 'SET NULL']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['reporter_id'],
            ['id'],
            ['onDelete' => 'SET NULL']
        );
    }
}
