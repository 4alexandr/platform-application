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

        call_user_func_array(array($obj, 'set'.ucfirst($property)), array($value));
        $this->assertEquals($value, call_user_func_array(array($obj, 'get'.ucfirst($property)), array()));
    }

    public function settersAndGettersDataProvider()
    {
        return array(
            array('code', 'ISS-1'),
            array('summary', 'Test summary'),
            array('description', 'Test Description'),
            array('priority', new Priority()),
            array('resolution', new Resolution()),
            array('type', new Type()),
            array('createdAt', new \DateTime()),
            array('updatedAt', new \DateTime()),
        );
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
}
