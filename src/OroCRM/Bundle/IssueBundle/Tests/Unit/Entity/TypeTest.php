<?php

namespace OroCRM\Bundle\IssueBundle\Tests\Unit\Entity;

use OroCRM\Bundle\IssueBundle\Entity\Type;

class TypeTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        new Type();
    }

    /**
     * @dataProvider settersAndGettersDataProvider
     */
    public function testSettersAndGetters($property, $value)
    {
        $type = new Type();

        call_user_func([$type, 'set'.ucfirst($property)], $value);
        $this->assertEquals($value, call_user_func([$type, 'get'.ucfirst($property)]));
    }

    public function settersAndGettersDataProvider()
    {
        return [
            ['name', 'story'],
            ['label', 'Story'],
            ['parent', new Type()],
        ];
    }

    public function testChildren()
    {
        $type = new Type();
        $child1 = new Type();
        $child2 = new Type();

        $this->assertEquals(0, $type->getChildren()->count());
        $this->assertEquals(null, $child1->getParent());
        $this->assertEquals(null, $child2->getParent());

        $child1->setParent($type);
        $child2->setParent($type);
        $type->addChild($child1);
        $type->addChild($child2);
        $type->addChild($child1);

        $this->assertEquals(2, $type->getChildren()->count());
        $this->assertEquals($type, $child1->getParent());
        $this->assertEquals($type, $child2->getParent());

        $type->removeChild($child1);
        $this->assertEquals(1, $type->getChildren()->count());
    }

    public function testToString()
    {
        $type = new Type();
        $type->setLabel('Story');

        $this->assertEquals('Story', (string)$type);
    }
}
