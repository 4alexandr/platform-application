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
        $this->createOrocrmIssueToRelatedIssueTable($schema);
        $this->createOrocrmPriorityTable($schema);
        $this->createOrocrmResolutionTable($schema);
        $this->createOrocrmTypeTable($schema);

        /* Foreign keys generation **/
        $this->addOrocrmIssueForeignKeys($schema);
        $this->addOrocrmIssueToCollaboratorForeignKeys($schema);
        $this->addOrocrmIssueToRelatedIssueForeignKeys($schema);
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
        $table->addColumn('priority_name', 'string', ['notnull' => false, 'length' => 32]);
        $table->addColumn('resolution_name', 'string', ['notnull' => false, 'length' => 32]);
        $table->addColumn('type_name', 'string', ['notnull' => false, 'length' => 32]);

        $table->setPrimaryKey(['id']);

        $table->addIndex(['owner_id'], 'IDX_EF1CE9717E3C61F9', []);
        $table->addIndex(['reporter_id'], 'IDX_EF1CE971E1CFE6F5', []);
        $table->addIndex(['priority_name'], 'IDX_EF1CE971965BD3DF', []);
        $table->addIndex(['resolution_name'], 'IDX_EF1CE9718EEEA2E1', []);
        $table->addIndex(['type_name'], 'IDX_EF1CE971892CBB0E', []);
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
     * Create orocrm_issue_to_related_issue table.
     *
     * @param Schema $schema
     */
    protected function createOrocrmIssueToRelatedIssueTable(Schema $schema)
    {
        $table = $schema->createTable('orocrm_issue_to_related_issue');

        $table->addColumn('issue_source', 'integer', []);
        $table->addColumn('issue_target', 'integer', []);

        $table->setPrimaryKey(['issue_source', 'issue_target']);

        $table->addIndex(['issue_source'], 'IDX_472432F6AD7AF554', []);
        $table->addIndex(['issue_target'], 'IDX_472432F6B49FA5DB', []);
    }

    /**
     * @param Schema $schema
     */
    protected function createOrocrmPriorityTable(Schema $schema)
    {
        $table = $schema->createTable('orocrm_issue_priority');

        $table->addColumn('name', 'string', ['notnull' => true, 'length' => 32]);
        $table->addColumn('label', 'string', ['notnull' => true, 'length' => 255]);
        $table->addColumn('`order`', 'integer', ['notnull' => true]);

        $table->setPrimaryKey(['name']);

        $table->addUniqueIndex(['label'], 'UNIQ_704D2F76EA750E8');
    }

    /**
     * @param Schema $schema
     */
    protected function createOrocrmResolutionTable(Schema $schema)
    {
        $table = $schema->createTable('orocrm_issue_resolution');

        $table->addColumn('name', 'string', ['notnull' => true, 'length' => 32]);
        $table->addColumn('label', 'string', ['notnull' => true, 'length' => 255]);

        $table->setPrimaryKey(['name']);

        $table->addUniqueIndex(['label'], 'UNIQ_7AC7D2D2EA750E8');
    }

    /**
     * @param Schema $schema
     */
    protected function createOrocrmTypeTable(Schema $schema)
    {
        $table = $schema->createTable('orocrm_issue_type');

        $table->addColumn('name', 'string', ['notnull' => true, 'length' => 32]);
        $table->addColumn('label', 'string', ['notnull' => true, 'length' => 255]);

        $table->setPrimaryKey(['name']);

        $table->addUniqueIndex(['label'], 'UNIQ_72E455C8EA750E8');
    }

    /**
     * @param Schema $schema
     */
    protected function addOrocrmIssueForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('orocrm_issue');

        $table->addForeignKeyConstraint(
            $schema->getTable('orocrm_issue_priority'),
            ['priority_name'],
            ['name'],
            ['onDelete' => 'SET NULL']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('orocrm_issue_resolution'),
            ['resolution_name'],
            ['name'],
            ['onDelete' => 'SET NULL']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('orocrm_issue_type'),
            ['type_name'],
            ['name'],
            ['onDelete' => 'SET NULL']
        );
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

    /**
     * Add orocrm_issue_to_related_issue foreign keys.
     *
     * @param Schema $schema
     */
    protected function addOrocrmIssueToRelatedIssueForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('orocrm_issue_to_related_issue');

        $table->addForeignKeyConstraint(
            $schema->getTable('orocrm_issue'),
            ['issue_source'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('orocrm_issue'),
            ['issue_target'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }
}
