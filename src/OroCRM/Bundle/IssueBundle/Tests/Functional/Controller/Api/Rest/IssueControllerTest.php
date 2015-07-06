<?php

namespace OroCRM\Bundle\IssueBundle\Tests\Functional\Controller\Api\Rest;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

/**
 * @outputBuffering enabled
 * @dbIsolation
 * @dbReindex
 */
class IssueControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->initClient([], $this->generateWsseAuthHeader());
    }

    public function testCreate()
    {
        $this->client->request('POST', $this->getUrl('orocrm_api_post_issue'), [
            'code' => 'ISS-1',
            'summary' => 'New issue',
            'priority' => 'major',
            'type' => 'story',
            'owner' => '1',
        ]);
        $issue = $this->getJsonResponseContent($this->client->getResponse(), 201);

        return $issue['id'];
    }

    /**
     * @depends testCreate
     *
     * @param int $id
     */
    public function testCreateChild($id)
    {
        $this->client->request('POST', $this->getUrl('orocrm_api_post_issue'), [
            'code' => 'SUB-1',
            'summary' => 'New subtask',
            'priority' => 'major',
            'type' => 'subtask',
            'owner' => '1',
            'parent' => $id,
        ]);
        $issue = $this->getJsonResponseContent($this->client->getResponse(), 201);

        return $issue['id'];
    }

    /**
     * @depends testCreateChild
     */
    public function testCget()
    {
        $this->client->request('GET', $this->getUrl('orocrm_api_get_issues'));
        $issues = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $this->assertCount(2, $issues);
    }

    /**
     * @depends testCreate
     *
     * @param int $id
     */
    public function testGet($id)
    {
        $this->client->request('GET', $this->getUrl('orocrm_api_get_issue', ['id' => $id]));
        $issue = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $this->assertEquals('ISS-1', $issue['code']);
    }

    /**
     * @depends testCreate
     *
     * @param int $id
     */
    public function testPut($id)
    {
        $updatedIssue = [
            'resolution' => 'done',
        ];
        $this->client->request('PUT', $this->getUrl('orocrm_api_put_issue', ['id' => $id]), $updatedIssue);
        $result = $this->client->getResponse();
        $this->assertEmptyResponseStatusCodeEquals($result, 204);

        $this->client->request('GET', $this->getUrl('orocrm_api_get_issue', ['id' => $id]));

        $issue = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $this->assertEquals('done', $issue['resolution']);
    }

    /**
     * @depends testCreate
     *
     * @param int $id
     */
    public function testDelete($id)
    {
        $this->client->request('DELETE', $this->getUrl('orocrm_api_delete_issue', ['id' => $id]));
        $result = $this->client->getResponse();
        $this->assertEmptyResponseStatusCodeEquals($result, 204);

        $this->client->request('GET', $this->getUrl('orocrm_api_get_issue', ['id' => $id]));
        $result = $this->client->getResponse();
        $this->assertJsonResponseStatusCodeEquals($result, 404);
    }
}
