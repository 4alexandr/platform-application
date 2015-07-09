<?php

namespace OroCRM\Bundle\IssueBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * IssueRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class IssueRepository extends EntityRepository
{
	/**
     * Get issues by status
     *
     * @return array
     */
    public function getCountByStatus()
    {
        $qb = $this->getEntityManager()
        	->getRepository('OroWorkflowBundle:WorkflowStep')
        	->createQueryBuilder('workflow')
        	->select('workflow.label as label, workflow.name, COUNT(issue) as issue_count')
        	->leftJoin('OroCRMIssueBundle:Issue', 'issue', 'WITH', 'issue.workflowStep = workflow')
        	->groupBy('workflow.name')
        	->orderBy('workflow.label', 'ASC');

        $data = $qb->getQuery()->getArrayResult();

        return $data;
    }
}
