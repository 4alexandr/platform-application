<?php

namespace OroCRM\Bundle\IssueBundle\Tests\Unit\Entity;

use OroCRM\Bundle\IssueBundle\Entity\Issue;
use Oro\Bundle\UserBundle\Entity\User;
use OroCRM\Bundle\IssueBundle\Entity\Priority;
use OroCRM\Bundle\IssueBundle\Entity\Resolution;
use OroCRM\Bundle\IssueBundle\Entity\Type;

class IssueTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        new Issue();
    }

    /**
     * @dataProvider settersAndGettersDataProvider
     */
    public function testSettersAndGetters($property, $value)
    {
        $obj = new Issue();

        call_user_func([$obj, 'set'.ucfirst($property)], $value);
        $this->assertEquals($value, call_user_func([$obj, 'get'.ucfirst($property)]));
    }

    public function settersAndGettersDataProvider()
    {
        return [
            ['code', 'ISS-1'],
            ['summary', 'Test summary'],
            ['description', 'Test Description'],
            ['priority', new Priority()],
            ['resolution', new Resolution()],
            ['type', new Type()],
            ['createdAt', new \DateTime()],
            ['updatedAt', new \DateTime()],
        ];
    }

    public function testPrePersist()
    {
        $obj = new Issue();

        $this->assertNull($obj->getCreatedAt());
        $this->assertNull($obj->getUpdatedAt());

        $obj->prePersist();
        $this->assertInstanceOf('\DateTime', $obj->getCreatedAt());
        $this->assertInstanceOf('\DateTime', $obj->getUpdatedAt());
    }

    public function testPreUpdate()
    {
        $obj = new Issue();

        $this->assertNull($obj->getUpdatedAt());

        $obj->preUpdate();
        $this->assertInstanceOf('\DateTime', $obj->getUpdatedAt());
    }

    public function testCollaborators()
    {
        $obj = new Issue();
        $owner = new User();
        $reporter = new User();

        $this->assertEquals(0, $obj->getCollaborators()->count());

        $obj->setOwner($owner);
        $obj->setReporter($reporter);
        $this->assertEquals(2, $obj->getCollaborators()->count());
    }

    public function testRelatedIssues()
    {
        $obj = new Issue();
        $related1 = new Issue();
        $related2 = new Issue();

        $this->assertEquals(0, $obj->getRelatedIssues()->count());

        $obj->addRelatedIssue($related1);
        $obj->addRelatedIssue($related2);
        $obj->addRelatedIssue($related1);

        $this->assertEquals(2, $obj->getRelatedIssues()->count());
    }

    public function testChildren()
    {
        $obj = new Issue();
        $related1 = new Issue();
        $related2 = new Issue();

        $this->assertEquals(0, $obj->getChildren()->count());
        $this->assertEquals(null, $related1->getParent());
        $this->assertEquals(null, $related2->getParent());

        $related1->setParent($obj);
        $related2->setParent($obj);
        $obj->addChild($related1);
        $obj->addChild($related2);
        $obj->addChild($related1);

        $this->assertEquals(2, $obj->getChildren()->count());
        $this->assertEquals($obj, $related1->getParent());
        $this->assertEquals($obj, $related2->getParent());
    }

    public function testGetSetWorkflowItem()
    {
        $entity = new Issue();
        $workflowItem = $this->getMock('Oro\Bundle\WorkflowBundle\Entity\WorkflowItem');

        $this->assertNull($entity->getWorkflowItem());

        $entity->setWorkflowItem($workflowItem);

        $this->assertEquals($workflowItem, $entity->getWorkflowItem());
    }

    public function testGetSetWorkflowStep()
    {
        $entity = new Issue();
        $workflowStep = $this->getMock('Oro\Bundle\WorkflowBundle\Entity\WorkflowStep');

        $this->assertNull($entity->getWorkflowStep());

        $entity->setWorkflowStep($workflowStep);

        $this->assertEquals($workflowStep, $entity->getWorkflowStep());
    }
}
