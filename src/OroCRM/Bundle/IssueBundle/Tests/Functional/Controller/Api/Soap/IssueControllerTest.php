<?php

namespace OroCRM\Bundle\IssueBundle\Tests\Functional\Controller\Api\Soap;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

/**
 * @outputBuffering enabled
 * @dbIsolation
 */
class IssueControllerTest extends WebTestCase
{
    /**
     * @var array
     */
    protected $issue = [
        'code' => 'ISS-1',
        'summary' => 'New issue',
        'priority' => 'major',
        'owner' => '1',
    ];

    protected function setUp()
    {
        $this->initClient([], $this->generateWsseAuthHeader());
        $this->initSoapClient();
    }

    /**
     * @return int
     */
    public function testCreate()
    {
        $result = $this->soapClient->createIssue($this->issue + ['type' => 'story']);

        $this->assertInternalType('int', $result);
        $this->assertGreaterThan(0, $result, $this->soapClient->__getLastResponse());

        return $result;
    }

    /**
     * @depends testCreate
     *
     * @param int $id
     */
    public function testCreateChild($id)
    {
        $result = $this->soapClient->createIssue(array_replace($this->issue, [
            'code' => 'SUB-1',
            'type' => 'subtask',
            'parent' => $id,
            'related_issues' => [$id],
        ]));

        $this->assertInternalType('int', $result);
        $this->assertGreaterThan(0, $result, $this->soapClient->__getLastResponse());

        return $result;
    }

    /**
     * @depends testCreateChild
     */
    public function testCget()
    {
        $issues = $this->soapClient->getIssues();
        $issues = $this->valueToArray($issues);

        $this->assertCount(1, $issues);
    }

    /**
     * @param int $id
     * @depends testCreateChild
     */
    public function testGet($id)
    {
        $issue = $this->soapClient->getIssue($id);
        $issue = $this->valueToArray($issue);
        $this->assertEquals('SUB-1', $issue['code']);
    }

    /**
     * @depends testCreateChild
     *
     * @param int $id
     */
    public function testUpdate($id)
    {
        $issue = array_replace($this->issue, ['code' => 'SUB-1.1']);

        $result = $this->soapClient->updateIssue($id, $issue);
        $this->assertTrue($result);

        $updatedIssue = $this->soapClient->getIssue($id);
        $updatedIssue = $this->valueToArray($updatedIssue);

        $this->assertEquals('SUB-1.1', $updatedIssue['code']);
    }

    /**
     * @param int $id
     * @depends testCreateChild
     */
    public function testDelete($id)
    {
        $result = $this->soapClient->deleteIssue($id);
        $this->assertTrue($result);

        $this->setExpectedException('\SoapFault', 'Record with ID "'.$id.'" can not be found');
        $this->soapClient->getIssue($id);
    }
}
