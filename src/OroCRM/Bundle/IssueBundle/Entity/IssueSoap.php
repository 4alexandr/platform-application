<?php

namespace OroCRM\Bundle\IssueBundle\Entity;

use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use Oro\Bundle\SoapBundle\Entity\SoapEntityInterface;

/**
 * @Soap\Alias("OroCRM.Bundle.IssueBundle.Entity.Issue")
 */
class IssueSoap extends Issue implements SoapEntityInterface
{
    /**
     * @param Issue $issue
     */
    public function soapInit($issue)
    {
        $properties = get_object_vars($issue);
        foreach ($properties as $property => $value) {
            $this->$property = $value;
        }

        $this->type = $this->getEntityName($this->type);
        $this->priority = $this->getEntityName($this->priority);
        $this->resolution = $this->getEntityName($this->resolution);

        $this->collaborators = $this->getEntitiesId($this->collaborators);
        $this->related_issues = $this->getEntitiesId($this->related_issues);
        $this->children = $this->getEntitiesId($this->children);
    }

    /**
     * @param Collection $entities
     *
     * @return int[]
     */
    protected function getEntitiesId($entities)
    {
        return $entities->map(function ($entity) {
            return $entity->getId();
        })->toArray();
    }

    /**
     * @param object $entity
     *
     * @return string|null
     */
    protected function getEntityName($entity)
    {
        if ($entity) {
            return $entity->getName();
        }

        return;
    }
}
