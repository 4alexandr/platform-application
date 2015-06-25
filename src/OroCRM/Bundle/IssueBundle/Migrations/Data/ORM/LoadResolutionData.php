<?php

namespace OroCRM\Bundle\IssueBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OroCRM\Bundle\IssueBundle\Entity\Resolution;

class LoadResolutionData implements FixtureInterface
{
    /**
     * @var array
     */
    protected $data = array(
        array(
            'name' => 'fixed',
            'label' => 'Fixed',
        ),
        array(
            'name' => 'wont_fix',
            'label' => 'Won\'t Fix',
        ),
        array(
            'name' => 'duplicate',
            'label' => 'Duplicate',
        ),
        array(
            'name' => 'incomplete',
            'label' => 'Incomplete',
        ),
        array(
            'name' => 'cannot_reproduce',
            'label' => 'Cannot Reproduce',
        ),
        array(
            'name' => 'done',
            'label' => 'Done',
        ),
        array(
            'name' => 'wont_do',
            'label' => 'Won\'t Do',
        ),
    );

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->data as $resolution) {
            if (!$this->isResolutionExist($manager, $resolution['name'])) {
                $entity = new Resolution();

                $entity->setName($resolution['name']);
                $entity->setLabel($resolution['label']);

                $manager->persist($entity);
            }
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     * @param string        $name
     *
     * @return bool
     */
    private function isResolutionExist(ObjectManager $manager, $name)
    {
        return count($manager->getRepository('OroCRMIssueBundle:Resolution')->find($name)) > 0;
    }
}
