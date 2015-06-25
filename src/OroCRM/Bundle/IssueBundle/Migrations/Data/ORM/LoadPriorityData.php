<?php

namespace OroCRM\Bundle\IssueBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OroCRM\Bundle\IssueBundle\Entity\Priority;

class LoadPriorityData implements FixtureInterface
{
    /**
     * @var array
     */
    protected $data = array(
        array(
            'label' => 'Trivial',
            'name' => 'trivial',
            'order' => 0,
        ),
        array(
            'label' => 'Major',
            'name' => 'major',
            'order' => 1,
        ),
        array(
            'label' => 'Critical',
            'name' => 'critical',
            'order' => 2,
        ),
        array(
            'label' => 'Blocker',
            'name' => 'blocker',
            'order' => 3,
        ),
    );

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->data as $priority) {
            if (!$this->isPriorityExist($manager, $priority['name'])) {
                $entity = new Priority();

                $entity->setname($priority['name']);
                $entity->setLabel($priority['label']);
                $entity->setOrder($priority['order']);

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
    private function isPriorityExist(ObjectManager $manager, $name)
    {
        return count($manager->getRepository('OroCRMIssueBundle:Priority')->find($name)) > 0;
    }
}
