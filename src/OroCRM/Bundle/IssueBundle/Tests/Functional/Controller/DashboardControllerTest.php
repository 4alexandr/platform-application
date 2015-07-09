<?php

namespace OroCRM\Bundle\IssueBundle\Tests\Functional\Controller;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

/**
 * @outputBuffering enabled
 * @dbIsolation
 * @dbReindex
 */
class DashboardControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->initClient([], $this->generateBasicAuthHeader());
    }

    public function testByStatusAction()
    {
        $crawler = $this->client->request('GET',
            $this->getUrl('orocrm_issue_dashboard_by_status_chart', ['widget' => 'issues_by_status'])
        );

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        $this->assertContains('Issues by status', $result->getContent());
    }
}
