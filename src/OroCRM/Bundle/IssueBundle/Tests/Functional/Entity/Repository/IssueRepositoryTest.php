<?php

namespace OroCRM\Bundle\IssueBundle\Tests\Functional\Entity\Repository;

use OroCRM\Bundle\IssueBundle\Entity\Repository\IssueRepository;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

/**
 * @dbIsolation
 * @dbReindex
 */
class IssueRepositoryTest extends WebTestCase
{
    /**
     * @var IssueRepository
     */
    protected $repository;

    protected function setUp()
    {
        $this->initClient();
        $this->loadFixtures(['OroCRM\Bundle\IssueBundle\Tests\Functional\DataFixtures\LoadIssueData']);
        $this->repository = $this->getContainer()->get('doctrine')->getRepository('OroCRMIssueBundle:Issue');
    }

    public function testGetCountByStatus()
    {
        $data = $this->repository->getCountByStatus();

        $this->assertEquals(4, count($data));

        for ($i = 0, $i_max = count($data); $i < $i_max; ++$i) {
            if ($data[$i]['name'] === 'open') {
                $this->assertGreaterThan(0, $data[$i]['issue_count']);
            } else {
                $this->assertEquals(0, $data[$i]['issue_count']);
            }
        }
    }
}
