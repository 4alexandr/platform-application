<?php

namespace OroCRM\Bundle\IssueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;
use OroCRM\Bundle\IssueBundle\Model\ExtendIssue;
use Oro\Bundle\UserBundle\Entity\User;

/**
 * Issue.
 *
 * @ORM\Table("orocrm_issue")
 * @ORM\Entity(repositoryClass="OroCRM\Bundle\IssueBundle\Entity\Repository\IssueRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Config(
 *  defaultValues={
 *      "ownership"={
 *          "owner_type"="USER",
 *          "owner_field_name"="owner",
 *          "owner_column_name"="owner_id"
 *      }
 *  }
 * )
 */
class Issue extends ExtendIssue
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $summary;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     * @ConfigField(
     *      defaultValues={
     *          "entity"={
     *              "label"="oro.ui.created_at"
     *          }
     *      }
     * )
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     * @ConfigField(
     *      defaultValues={
     *          "entity"={
     *              "label"="oro.ui.updated_at"
     *          }
     *      }
     * )
     */
    private $updatedAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $owner;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="reporter_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $reporter;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinTable(name="orocrm_issue_to_collaborator")
     */
    protected $collaborators;

    public function __construct()
    {
        parent::__construct();

        $this->collaborators = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set code.
     *
     * @param string $code
     *
     * @return Issue
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set summary.
     *
     * @param string $summary
     *
     * @return Issue
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary.
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Issue
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Issue
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return Issue
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdAt = $this->createdAt ?: new \DateTime('now', new \DateTimeZone('UTC'));
        $this->updatedAt = clone $this->createdAt;
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime('now', new \DateTimeZone('UTC'));
    }

    public function __toString()
    {
        return $this->getCode().': '.$this->getSummary();
    }

    /**
     * Get owner.
     *
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set owner.
     *
     * @param User $owner
     *
     * @return Issue
     */
    public function setOwner($owner = null)
    {
        if ($owner) {
            $this->addCollaborator($owner);
        }
        $this->owner = $owner;
    }

    /**
     * Get reporter.
     *
     * @return User
     */
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     * Set reporter.
     *
     * @param User $reporter
     *
     * @return Issue
     */
    public function setReporter($reporter = null)
    {
        if ($reporter) {
            $this->addCollaborator($reporter);
        }
        $this->reporter = $reporter;
    }

    /**
     * Add collaborator.
     *
     * @param User
     *
     * @return Issue
     */
    public function addCollaborator(User $collaborator)
    {
        if (!$this->getCollaborators()->contains($collaborator)) {
            $this->collaborators[] = $collaborator;
        }

        return $this;
    }

    /**
     * Remove collaborator.
     *
     * @param User $collaborators
     */
    public function removeCollaborator(User $collaborator)
    {
        $this->collaborators->removeElement($collaborators);
    }

    /**
     * Get collaborators.
     *
     * @return Collection
     */
    public function getCollaborators()
    {
        return $this->collaborators;
    }
}
