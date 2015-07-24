<?php

namespace OroCRM\Bundle\IssueBundle\Tests\Unit\Entity;

use Oro\Bundle\UserBundle\Entity\User;

use OroCRM\Bundle\IssueBundle\Entity\Issue;
use OroCRM\Bundle\IssueBundle\Entity\IssueSoap;
use OroCRM\Bundle\IssueBundle\Entity\Type;

class IssueSoapTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        new IssueSoap();
    }

    public function testSoapInit()
    {
        $issueSoap = new IssueSoap();

        $issue = new Issue();
        $issue->setId(1);

        $relatedIssue = new Issue();
        $relatedIssue ->setId(2);

        $user = new User();
        $user->setId(1);

        $type = new Type();
        $type->setName('bug');

        $properties = [
            'code' => 'ISS-1',
            'owner' => $user,
            'type' => $type,
        ];

        foreach ($properties as $property => $value) {
            $setter = 'set' . ucfirst($property);
            $issue->$setter($value);
        }

        $issue->addRelatedIssue($relatedIssue);

        $issueSoap->soapInit($issue);

        $this->assertEquals($issueSoap->getCode(), $issue->getCode());
        $this->assertEquals($issueSoap->getOwner(), $issue->getOwner());
        $this->assertEquals($issueSoap->getType(), $issue->getType()->getName());
        $this->assertEquals($issueSoap->getRelatedIssues(), [$relatedIssue->getId()]);
    }
}
