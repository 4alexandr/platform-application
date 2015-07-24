<?php
namespace OroCRM\Bundle\IssueBundle\Tests\Unit\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use OroCRM\Bundle\IssueBundle\Form\Type\IssueSelectType;

class IssueSelectTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IssueSelectType
     */
    protected $type;

    /**
     * Setup test env
     */
    protected function setUp()
    {
        $this->type = new IssueSelectType();
    }

    public function testSetDefaultOptions()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|OptionsResolverInterface $resolver */
        $resolver = $this->getMock('Symfony\Component\OptionsResolver\OptionsResolverInterface');
        $resolver->expects($this->once())
            ->method('setDefaults')
            ->with($this->isType('array'));

        $this->type->setDefaultOptions($resolver);
    }

    public function testGetParent()
    {
        $this->assertEquals(
            'oro_entity_create_or_select_inline',
            $this->type->getParent()
        );
    }

    public function testGetName()
    {
        $this->assertEquals('orocrm_issue_select', $this->type->getName());
    }
}
