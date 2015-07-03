<?php

namespace OroCRM\Bundle\IssueBundle\ImportExport\TemplateFixture;

use Oro\Bundle\ImportExportBundle\TemplateFixture\AbstractTemplateRepository;
use Oro\Bundle\ImportExportBundle\TemplateFixture\TemplateFixtureInterface;
use OroCRM\Bundle\IssueBundle\Entity\Issue;
use OroCRM\Bundle\IssueBundle\Entity\Priority;
use OroCRM\Bundle\IssueBundle\Entity\Resolution;
use OroCRM\Bundle\IssueBundle\Entity\Type;

class IssueFixture extends AbstractTemplateRepository implements TemplateFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEntityClass()
    {
        return 'OroCRM\Bundle\IssueBundle\Entity\Issue';
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->getEntityData('ISS-1');
    }

    /**
     * {@inheritdoc}
     */
    protected function createEntity($key)
    {
        return new Issue();
    }

    /**
     * @param string $key
     * @param Issue  $entity
     */
    public function fillEntityData($key, $entity)
    {
        $userRepo = $this->templateManager
            ->getEntityRepository('Oro\Bundle\UserBundle\Entity\User');
        $issueRepo = $this->templateManager
            ->getEntityRepository('OroCRM\Bundle\IssueBundle\Entity\Issue');

        $priority = new Priority();
        $priority->setName('major');

        $resolution = new Resolution();
        $resolution->setName('fixed');

        $type = new Type();
        $type->setName('subtask');

        $parent = new Issue();
        $parent->setCode('ISS-2');

        switch ($key) {
            case 'ISS-1':
                $entity
                    ->setCode($key)
                    ->setSummary('Issue 1')
                    ->setDescription('Issue 1 description')
                    ->setOwner($userRepo->getEntity('John Doo'))
                    ->setReporter($userRepo->getEntity('John Doo'))
                    ->setCreatedAt(new \DateTime('now', new \DateTimeZone('UTC')))
                    ->setUpdatedAt(new \DateTime('now', new \DateTimeZone('UTC')))
                    ->setPriority($priority)
                    ->setResolution($resolution)
                    ->setType($type)
                    ->setParent($parent)
                    ;

                return;
        }

        parent::fillEntityData($key, $entity);
    }
}
