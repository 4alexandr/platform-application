<?php

namespace OroCRM\Bundle\IssueBundle\Tests\Unit\Entity;

use OroCRM\Bundle\IssueBundle\Entity\Priority;

class PriorityTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        new Priority();
    }

    /**
     * @dataProvider settersAndGettersDataProvider
     */
    public function testSettersAndGetters($property, $value)
    {
        $priority = new Priority();

        call_user_func([$priority, 'set'.ucfirst($property)], $value);
        $this->assertEquals($value, call_user_func([$priority, 'get'.ucfirst($property)]));
    }

    public function settersAndGettersDataProvider()
    {
        return [
            ['name', 'major'],
            ['label', 'Major'],
            ['order', 1],
        ];
    }

    public function testToString()
    {
        $priority = new Priority();
        $priority->setLabel('Major');

        $this->assertEquals('Major', (string)$priority);
    }
}
