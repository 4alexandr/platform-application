<?php

namespace OroCRM\Bundle\IssueBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OroCRM\Bundle\IssueBundle\Entity\Type;

class LoadTypeData implements FixtureInterface
{
    /**
     * @var array
     */
    protected $data = array(
        array(
            'name' => 'bug',
            'label' => 'Bug',
        ),
        array(
            'name' => 'task',
            'label' => 'Task',
        ),
        array(
            'name' => 'story',
            'label' => 'Story',
        ),
        array(
            'name' => 'subtask',
            'label' => 'Subtask',
        ),
    );

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->data as $type) {
            if (!$this->isTypeExist($manager, $type['name'])) {
                $entity = new Type();

                $entity->setName($type['name']);
                $entity->setLabel($type['label']);

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
    private function isTypeExist(ObjectManager $manager, $name)
    {
        return count($manager->getRepository('OroCRMIssueBundle:Type')->find($name)) > 0;
    }
}
