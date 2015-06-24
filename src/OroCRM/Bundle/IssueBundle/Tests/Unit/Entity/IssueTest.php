<?php

namespace OroCRM\Bundle\IssueBundle\Tests\Unit\Entity;

use OroCRM\Bundle\IssueBundle\Entity\Issue;

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
}
