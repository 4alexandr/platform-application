<?php

namespace OroCRM\Bundle\IssueBundle\Tests\Form\Handler;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

use Oro\Bundle\TagBundle\Entity\TagManager;

use OroCRM\Bundle\IssueBundle\Entity\Issue;
use OroCRM\Bundle\IssueBundle\Form\Handler\IssueHandler;

class IssueHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|EntityManagerInterface
     */
    protected $manager;

    /**
     * @var IssueHandler
     */
    protected $handler;

    /**
     * @var Issue
     */
    protected $entity;

    protected function setUp()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|FormInterface $form */
        $form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = new Request();

        $this->manager = $this->getMockBuilder('Doctrine\ORM\EntityManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->handler = new IssueHandler($form, $this->request, $this->manager);

        /** @var \PHPUnit_Framework_MockObject_MockObject|TagManager $tagManager */
        $tagManager = $this->getMockBuilder('Oro\Bundle\TagBundle\Entity\TagManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->handler->setTagManager($tagManager);

        $this->entity  = new Issue();
    }

    public function testProcessUnsupportedRequest()
    {
        $this->handler->getForm()->expects($this->once())
            ->method('setData')
            ->with($this->entity);

        $this->handler->getForm()->expects($this->never())
            ->method('submit');

        $this->assertFalse($this->handler->process($this->entity));
    }

    /**
     * @dataProvider supportedMethods
     * @param string $method
     */
    public function testProcessSupportedRequest($method)
    {
        $this->handler->getForm()->expects($this->once())
            ->method('setData')
            ->with($this->entity);

        $this->request->setMethod($method);

        $this->handler->getForm()->expects($this->once())
            ->method('submit')
            ->with($this->request);

        $this->assertFalse($this->handler->process($this->entity));
    }

    public function supportedMethods()
    {
        return array(
            array('POST'),
            array('PUT')
        );
    }

    public function testProcessValidData()
    {
        $this->request->setMethod('POST');

        $this->handler->getForm()->expects($this->once())
            ->method('setData')
            ->with($this->entity);

        $this->handler->getForm()->expects($this->once())
            ->method('submit')
            ->with($this->request);

        $this->handler->getForm()->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->manager->expects($this->once())
            ->method('persist')
            ->with($this->entity);

        $this->manager->expects($this->once())
            ->method('flush');

        $this->assertTrue($this->handler->process($this->entity));
    }
}
