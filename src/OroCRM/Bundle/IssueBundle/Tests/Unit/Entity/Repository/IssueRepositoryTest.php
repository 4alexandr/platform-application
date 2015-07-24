<?php

namespace OroCRM\Bundle\IssueBundle\Tests\Unit\Entity\Repository;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

use Oro\Bundle\TestFrameworkBundle\Test\Doctrine\ORM\OrmTestCase;
use Oro\Bundle\TestFrameworkBundle\Test\Doctrine\ORM\Mocks\EntityManagerMock;

use OroCRM\Bundle\IssueBundle\Entity\Repository\IssueRepository;

class IssueRepositoryTest extends OrmTestCase
{
    /** @var EntityManagerMock */
    protected $em;

    protected function setUp()
    {
        $metadataDriver = new AnnotationDriver(
            new AnnotationReader(),
            [
                'OroCRM\Bundle\IssueBundle\Entity',
                'Oro\Bundle\WorkflowBundle\Entity',
            ]
        );

        $this->em = $this->getTestEntityManager();
        $this->em->getConfiguration()->setMetadataDriverImpl($metadataDriver);
        $this->em->getConfiguration()->setEntityNamespaces(
            [
                'OroCRMIssueBundle' => 'OroCRM\Bundle\IssueBundle\Entity',
                'OroWorkflowBundle' => 'Oro\Bundle\WorkflowBundle\Entity',
            ]
        );
    }

    public function testGetCountByStatus()
    {
        /** @var IssueRepository $repo */
        $repo = $this->em->getRepository('OroCRMIssueBundle:Issue');
        $qb   = $repo->getCountByStatus();

        $this->assertEquals(
            'SELECT workflow.label as label,'
            . ' workflow.name, COUNT(issue) as issue_count'
            . ' FROM Oro\Bundle\WorkflowBundle\Entity\WorkflowStep workflow'
            . ' LEFT JOIN OroCRMIssueBundle:Issue issue'
            . ' WITH issue.workflowStep = workflow'
            . ' GROUP BY workflow.name ORDER BY workflow.label ASC',
            $qb->getDQL()
        );
    }
}
