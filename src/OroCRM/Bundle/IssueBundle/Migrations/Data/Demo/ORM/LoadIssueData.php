<?php

namespace OroCRM\Bundle\IssueBundle\Migrations\Data\Demo\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OroCRM\Bundle\IssueBundle\Entity\Issue;

class LoadIssueData implements FixtureInterface
{
    const FIXTURES_COUNT = 20;

    /**
     * @var array
     */
    protected static $fixtureSummary = [
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
        'Mauris fringilla quam a sapien tristique, sit amet venenatis eros pulvinar.',
        'Donec luctus felis id tortor venenatis, ac viverra nisi vulputate.',
        'Proin faucibus dui sit amet aliquam molestie.',
        'Nam pretium nisl vel quam porttitor congue.',
        'Vestibulum quis felis dignissim, sollicitudin ipsum interdum, vulputate orci.',
        'Maecenas vel felis sodales, hendrerit metus ac, facilisis est.',
        'Vivamus sed eros quis tortor mattis ultrices.',
        'Phasellus cursus erat vel ante condimentum, et volutpat urna imperdiet.',
        'Donec a magna ullamcorper, lobortis ipsum vitae, mattis massa.',
        'Suspendisse euismod ex eget condimentum consectetur.',
        'Sed quis ante ultricies dui vulputate interdum.',
        'Donec non enim nec nisl dignissim dignissim.',
        'Donec ac odio et sapien imperdiet euismod.',
        'Mauris malesuada dolor in turpis gravida, eget dapibus dolor porta.',
        'Aenean dictum dolor in ultricies dapibus.',
        'Vivamus rhoncus velit id neque vehicula ultricies.',
        'Nam posuere est vel mattis venenatis.',
        'Nam bibendum ex iaculis blandit fermentum.',
        'Sed ullamcorper ipsum id ipsum laoreet vulputate.',
    ];

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->persistDemoIssues($manager);

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    protected function persistDemoIssues(ObjectManager $manager)
    {
        $owners = $manager->getRepository('OroUserBundle:User')->findAll();
        if (empty($owners)) {
            return;
        }

        $priorities = $manager->getRepository('OroCRMIssueBundle:Priority')->findAll();
        if (empty($priorities)) {
            return;
        }

        $resolutions = $manager->getRepository('OroCRMIssueBundle:Resolution')->findAll();
        if (empty($resolutions)) {
            return;
        }

        $types = $manager->getRepository('OroCRMIssueBundle:Type')->findAll();
        if (empty($types)) {
            return;
        }

        $issues = array();

        for ($i = 0; $i < self::FIXTURES_COUNT; ++$i) {
            if ($manager->getRepository('OroCRMIssueBundle:Issue')->findOneBySummary(self::$fixtureSummary[$i])) {
                // Issue with this summary is already exist
                continue;
            }

            $issue = new Issue();
            $issue->setCode('ISS-'.($i + 1));
            $issue->setSummary(self::$fixtureSummary[$i]);
            $issue->setDescription(str_repeat(self::$fixtureSummary[$i], 3));

            $issue->setPriority($this->getRandomEntity($priorities));
            $issue->setResolution($this->getRandomEntity($resolutions));
            $issue->setType($this->getRandomEntity($types));
            $issue->setOwner($this->getRandomEntity($owners));
            $issue->setReporter($this->getRandomEntity($owners));

            if (!empty($issues)) {
                $issue->addRelatedIssue($this->getRandomEntity($issues));
            }

            $manager->persist($issue);
            $issues[] = $issue;
        }
    }

    /**
     * @param array $entities
     *
     * @return object|null
     */
    protected function getRandomEntity($entities)
    {
        if (empty($entities)) {
            return;
        }

        return $entities[rand(0, count($entities) - 1)];
    }
}
