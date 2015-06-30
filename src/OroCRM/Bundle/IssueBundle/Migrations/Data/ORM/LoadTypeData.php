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
    protected $data = [
        [
            'name' => 'bug',
            'label' => 'Bug',
            'parent_name' => null,
        ],
        [
            'name' => 'task',
            'label' => 'Task',
            'parent_name' => null,
        ],
        [
            'name' => 'story',
            'label' => 'Story',
            'parent_name' => null,
        ],
        [
            'name' => 'subtask',
            'label' => 'Subtask',
            'parent_name' => 'story',
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $entities = [];
        foreach ($this->data as $type) {
            if (!$this->isTypeExist($manager, $type['name'])) {
                $entity = new Type();

                $entity->setName($type['name']);
                $entity->setLabel($type['label']);
                if (isset($entities[$type['parent_name']])) {
                    $entity->setParent($entities[$type['parent_name']]);
                }

                $manager->persist($entity);
                $entities[$entity->getName()] = $entity;
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
