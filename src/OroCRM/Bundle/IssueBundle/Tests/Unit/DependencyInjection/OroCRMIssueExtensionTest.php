<?php

namespace OroCRM\Bundle\IssueBundle\Tests\Unit\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use OroCRM\Bundle\IssueBundle\DependencyInjection\OroCRMIssueExtension;

class OroCRMIssueExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OroCRMIssueExtension
     */
    private $extension;

    /**
     * @var ContainerBuilder
     */
    private $container;

    protected function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->extension = new OroCRMIssueExtension();
    }

    public function testLoad()
    {
        $this->extension->load(array(), $this->container);
    }
}
