<?php

namespace OroCRM\Bundle\IssueBundle\Tests\Unit\Entity;

use OroCRM\Bundle\IssueBundle\Entity\Resolution;

class ResolutionTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        new Resolution();
    }

    /**
     * @dataProvider settersAndGettersDataProvider
     */
    public function testSettersAndGetters($property, $value)
    {
        $resolution = new Resolution();

        call_user_func([$resolution, 'set'.ucfirst($property)], $value);
        $this->assertEquals($value, call_user_func([$resolution, 'get'.ucfirst($property)]));
    }

    public function settersAndGettersDataProvider()
    {
        return [
            ['name', 'done'],
            ['label', 'Done'],
        ];
    }

    public function testToString()
    {
        $resolution = new Resolution();
        $resolution->setLabel('Done');

        $this->assertEquals('Done', (string)$resolution);
    }
}
