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

    public function testToString()
    {
        $issue = new Issue();
        $issue->setCode('CODE');
        $issue->setSummary('Summary');

        $this->assertEquals('CODE: Summary', (string)$issue);
    }

    public function testIsSupportChildren()
    {
        $issue = new Issue();

        $this->assertFalse($issue->isSupportChildren());
    }

    /**
     * @dataProvider settersAndGettersDataProvider
     */
    public function testSettersAndGetters($property, $value)
    {
        $issue = new Issue();

        call_user_func([$issue, 'set'.ucfirst($property)], $value);
        $this->assertEquals(
            $value,
            call_user_func([$issue, 'get'.ucfirst($property)])
        );
    }

    public function settersAndGettersDataProvider()
    {
        return [
            ['code', 'ISS-1'],
            ['summary', 'Test summary'],
            ['description', 'Test Description'],
            ['priority', new Priority()],
            ['resolution', new Resolution()],
            ['owner', new User()],
            ['reporter', new User()],
            ['type', new Type()],
            ['createdAt', new \DateTime()],
            ['updatedAt', new \DateTime()],
        ];
    }

    public function testIsEmpty()
    {
        $issue = new Issue();
        $this->assertTrue($issue->isEmpty());
        $this->assertNull($issue->getId());
        $this->assertNull($issue->getTaggableId());
    }

    public function testPrePersist()
    {
        $issue = new Issue();

        $this->assertNull($issue->getCreatedAt());
        $this->assertNull($issue->getUpdatedAt());

        $issue->prePersist();
        $this->assertInstanceOf('\DateTime', $issue->getCreatedAt());
        $this->assertInstanceOf('\DateTime', $issue->getUpdatedAt());
    }

    public function testPreUpdate()
    {
        $issue = new Issue();

        $this->assertNull($issue->getUpdatedAt());

        $issue->preUpdate();
        $this->assertInstanceOf('\DateTime', $issue->getUpdatedAt());
    }

    public function testCollaborators()
    {
        $issue = new Issue();
        $owner = new User();
        $reporter = new User();

        $this->assertEquals(0, $issue->getCollaborators()->count());

        $issue->setOwner($owner);
        $issue->setReporter($reporter);
        $this->assertEquals(2, $issue->getCollaborators()->count());

        $issue->removeCollaborator($reporter);
        $this->assertEquals(1, $issue->getCollaborators()->count());
    }

    public function testRelatedIssues()
    {
        $issue = new Issue();
        $related1 = new Issue();
        $related2 = new Issue();

        $this->assertEquals(0, $issue->getRelatedIssues()->count());

        $issue->addRelatedIssue($related1);
        $issue->addRelatedIssue($related2);
        $issue->addRelatedIssue($related1);

        $this->assertEquals(2, $issue->getRelatedIssues()->count());

        $issue->removeRelatedIssue($related1);
        $this->assertEquals(1, $issue->getRelatedIssues()->count());
    }

    public function testChildren()
    {
        $issue = new Issue();
        $related1 = new Issue();
        $related2 = new Issue();

        $this->assertEquals(0, $issue->getChildren()->count());
        $this->assertEquals(null, $related1->getParent());
        $this->assertEquals(null, $related2->getParent());

        $related1->setParent($issue);
        $related2->setParent($issue);
        $issue->addChild($related1);
        $issue->addChild($related2);
        $issue->addChild($related1);

        $this->assertEquals(2, $issue->getChildren()->count());
        $this->assertEquals($issue, $related1->getParent());
        $this->assertEquals($issue, $related2->getParent());

        $issue->removeChild($related1);
        $this->assertEquals(1, $issue->getChildren()->count());
    }

    public function testGetSetWorkflowItem()
    {
        $entity = new Issue();
        $workflowItem = $this->getMock(
            'Oro\Bundle\WorkflowBundle\Entity\WorkflowItem'
        );

        $this->assertNull($entity->getWorkflowItem());

        $entity->setWorkflowItem($workflowItem);

        $this->assertEquals($workflowItem, $entity->getWorkflowItem());
    }

    public function testGetSetWorkflowStep()
    {
        $entity = new Issue();
        $workflowStep = $this->getMock(
            'Oro\Bundle\WorkflowBundle\Entity\WorkflowStep'
        );

        $this->assertNull($entity->getWorkflowStep());

        $entity->setWorkflowStep($workflowStep);

        $this->assertEquals($workflowStep, $entity->getWorkflowStep());
    }

    public function testGetTags()
    {
        $issue = new Issue();
        $this->assertInstanceOf(
            'Doctrine\Common\Collections\ArrayCollection',
            $issue->getTags()
        );
        $this->assertTrue($issue->getTags()->isEmpty());

        $issue->setTags(['tag']);
        $this->assertEquals(['tag'], $issue->getTags());
    }

    public function testWithoutConstructor()
    {

        $ref = new \ReflectionClass('OroCRM\Bundle\IssueBundle\Entity\Issue');
        $issue = $ref->newInstanceWithoutConstructor();

        $this->assertInstanceOf(
            'Doctrine\Common\Collections\ArrayCollection',
            $issue->getCollaborators()
        );
        $this->assertInstanceOf(
            'Doctrine\Common\Collections\ArrayCollection',
            $issue->getRelatedIssues()
        );
        $this->assertInstanceOf(
            'Doctrine\Common\Collections\ArrayCollection',
            $issue->getChildren()
        );
    }
}
