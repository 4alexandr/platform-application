<?php

namespace OroCRM\Bundle\IssueBundle\Tests\Functional\Controller;

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
        $this->initClient([], $this->generateBasicAuthHeader());
    }

    public function testCreate()
    {
        $crawler = $this->client->request('GET', $this->getUrl('orocrm_issue_create'));

        $form = $crawler->selectButton('Save and Close')->form();
        $form['orocrm_issue[code]'] = 'ISS-1';
        $form['orocrm_issue[summary]'] = 'New issue';
        $form['orocrm_issue[description]'] = 'New description';
        $form['orocrm_issue[owner]'] = '1';
        $form['orocrm_issue[reporter]'] = '1';

        $this->client->followRedirects(true);
        $crawler = $this->client->submit($form);
        $result = $this->client->getResponse();

        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        $this->assertContains('Issue saved', $crawler->html());
    }

    /**
     * @depends testCreate
     */
    public function testUpdate()
    {
        $response = $this->client->requestGrid(
            'issues-grid',
            ['issues-grid[_filter][code][value]' => 'ISS-1']
        );

        $result = $this->getJsonResponseContent($response, 200);
        $result = reset($result['data']);

        $crawler = $this->client->request(
            'GET',
            $this->getUrl('orocrm_issue_update', ['id' => $result['id']])
        );

        $form = $crawler->selectButton('Save and Close')->form();
        $form['orocrm_issue[summary]'] = 'Issue updated';
        $form['orocrm_issue[description]'] = 'Description updated';

        $this->client->followRedirects(true);
        $crawler = $this->client->submit($form);
        $result = $this->client->getResponse();

        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        $this->assertContains('Issue saved', $crawler->html());
    }

    /**
     * @depends testUpdate
     */
    public function testView()
    {
        $response = $this->client->requestGrid(
            'issues-grid',
            ['issues-grid[_filter][code][value]' => 'ISS-1']
        );

        $result = $this->getJsonResponseContent($response, 200);
        $result = reset($result['data']);

        $this->client->request(
            'GET',
            $this->getUrl('orocrm_issue_view', ['id' => $result['id']])
        );
        $result = $this->client->getResponse();

        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        $this->assertContains('Issue updated - Issue', $result->getContent());
    }

    /**
     * @depends testUpdate
     */
    public function testIndex()
    {
        $this->client->request('GET', $this->getUrl('orocrm_issue_index'));
        $result = $this->client->getResponse();

        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        $this->assertContains('Issue updated', $result->getContent());
    }
}
