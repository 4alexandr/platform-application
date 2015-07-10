<?php

namespace OroCRM\Bundle\IssueBundle\Tests\Unit\ImportExport\TemplateFixture;

use Oro\Bundle\ImportExportBundle\TemplateFixture\TemplateEntityRegistry;
use Oro\Bundle\ImportExportBundle\TemplateFixture\TemplateManager;
use Oro\Bundle\UserBundle\ImportExport\TemplateFixture\UserFixture;
use Oro\Bundle\OrganizationBundle\ImportExport\TemplateFixture\BusinessUnitFixture;
use OroCRM\Bundle\IssueBundle\Entity\Issue;
use OroCRM\Bundle\IssueBundle\ImportExport\TemplateFixture\IssueFixture;

class IssueFixtureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IssueFixture
     */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new IssueFixture();
    }

    public function testGetEntityClass()
    {
        $this->assertEquals('OroCRM\Bundle\IssueBundle\Entity\Issue', $this->fixture->getEntityClass());
    }

    public function testCreateEntity()
    {
        $this->fixture->setTemplateManager($this->getTemplateManager());

        $this->assertEquals(new Issue(), $this->fixture->getEntity('ISS-1'));
    }

    /**
     * @param string $key
     *
     * @dataProvider fillEntityDataProvider
     */
    public function testFillEntityData($key)
    {
        $this->fixture->setTemplateManager($this->getTemplateManager());

        $issue = new Issue();

        $this->fixture->fillEntityData($key, $issue);
        $this->assertEquals($key, $issue->getCode());
    }

    /**
     * @return array
     */
    public function fillEntityDataProvider()
    {
        return array(
            'ISS-1' => array(
                'key' => 'ISS-1',
            ),
        );
    }

    public function testGetData()
    {
        $this->fixture->setTemplateManager($this->getTemplateManager());

        $data = $this->fixture->getData();
        $this->assertCount(1, $data);

        /** @var Issue $issue */
        $issue = current($data);
        $this->assertInstanceOf('OroCRM\Bundle\IssueBundle\Entity\Issue', $issue);
        $this->assertEquals('ISS-1', $issue->getCode());
    }

    /**
     * @return TemplateManager
     */
    protected function getTemplateManager()
    {
        $entityRegistry = new TemplateEntityRegistry();
        $templateManager = new TemplateManager($entityRegistry);
        $templateManager->addEntityRepository(new UserFixture());
        $templateManager->addEntityRepository(new BusinessUnitFixture());
        $templateManager->addEntityRepository($this->fixture);

        return $templateManager;
    }
}
