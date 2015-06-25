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
        $this->createOrocrmIssueToCollaboratorTable($schema);

        /* Foreign keys generation **/
        $this->addOrocrmIssueForeignKeys($schema);
        $this->addOrocrmIssueToCollaboratorForeignKeys($schema);
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
     * Create orocrm_issue_to_collaborator table.
     *
     * @param Schema $schema
     */
    protected function createOrocrmIssueToCollaboratorTable(Schema $schema)
    {
        $table = $schema->createTable('orocrm_issue_to_collaborator');

        $table->addColumn('issue_id', 'integer', []);
        $table->addColumn('user_id', 'integer', []);

        $table->setPrimaryKey(['issue_id', 'user_id']);

        $table->addIndex(['issue_id'], 'IDX_7480BCE5E7AA5', []);
        $table->addIndex(['user_id'], 'IDX_7480BCEA76ED395', []);
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

    /**
     * Add orocrm_issue_to_collaborator foreign keys.
     *
     * @param Schema $schema
     */
    protected function addOrocrmIssueToCollaboratorForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('orocrm_issue_to_collaborator');
        $table->addForeignKeyConstraint(
            $schema->getTable('orocrm_issue'),
            ['issue_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['user_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }
}
