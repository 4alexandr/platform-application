<?php

namespace OroCRM\Bundle\IssueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;
use Oro\Bundle\UserBundle\Entity\User;
use OroCRM\Bundle\IssueBundle\Model\ExtendIssue;
use Oro\Bundle\FormBundle\Entity\EmptyItem;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowItem;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowStep;
use Oro\Bundle\TagBundle\Entity\Taggable;

/**
 * Issue.
 *
 * @ORM\Table("orocrm_issue")
 * @ORM\Entity(repositoryClass="OroCRM\Bundle\IssueBundle\Entity\Repository\IssueRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Config(
 *     defaultValues={
 *         "ownership"={
 *             "owner_type"="USER",
 *             "owner_field_name"="owner",
 *             "owner_column_name"="owner_id"
 *         },
 *         "workflow"={
 *             "active_workflow"="issue_flow"
 *         },
 *     }
 * )
 */
class Issue extends ExtendIssue implements EmptyItem, Taggable
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Soap\ComplexType("int", nillable=true)
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "order"=10
     *          }
     *      }
     * )
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Soap\ComplexType("string")
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "order"=20,
     *              "identity"=true
     *          }
     *      }
     * )
     */
    protected $code;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Soap\ComplexType("string")
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "order"=30
     *          }
     *      }
     * )
     */
    protected $summary;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     * @Soap\ComplexType("string", nillable=true)
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "order"=40
     *          }
     *      }
     * )
     */
    protected $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     * @Soap\ComplexType("dateTime", nillable=true)
     * @ConfigField(
     *      defaultValues={
     *          "entity"={
     *              "label"="oro.ui.created_at"
     *          },
     *          "importexport"={
     *              "order"=120
     *          }
     *      }
     * )
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     * @Soap\ComplexType("dateTime", nillable=true)
     * @ConfigField(
     *      defaultValues={
     *          "entity"={
     *              "label"="oro.ui.updated_at"
     *          },
     *          "importexport"={
     *              "order"=130
     *          }
     *      }
     * )
     */
    protected $updatedAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="SET NULL")
     * @Soap\ComplexType("int", nillable=true)
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "order"=100,
     *              "short"=true
     *          }
     *      }
     * )
     */
    protected $owner;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="reporter_id", referencedColumnName="id", onDelete="SET NULL")
     * @Soap\ComplexType("int", nillable=true)
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "order"=90,
     *              "short"=true
     *          }
     *      }
     * )
     */
    protected $reporter;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinTable(name="orocrm_issue_to_collaborator")
     * @Soap\ComplexType("int[]", nillable=true)
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     */
    protected $collaborators;

    /**
     * @var priority
     *
     * @ORM\ManyToOne(targetEntity="Priority")
     * @ORM\JoinColumn(name="priority_name", referencedColumnName="name", onDelete="SET NULL")
     * @Soap\ComplexType("string")
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "order"=60,
     *              "short"=true
     *          }
     *      }
     * )
     */
    protected $priority;

    /**
     * @var resolution
     *
     * @ORM\ManyToOne(targetEntity="Resolution")
     * @ORM\JoinColumn(name="resolution_name", referencedColumnName="name", onDelete="SET NULL")
     * @Soap\ComplexType("string", nillable=true)
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "order"=80,
     *              "short"=true
     *          }
     *      }
     * )
     */
    protected $resolution;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Issue")
     * @ORM\JoinTable(name="orocrm_issue_to_related_issue")
     * @Soap\ComplexType("int[]", nillable=true)
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     */
    protected $related_issues;

    /**
     * @var type
     *
     * @ORM\ManyToOne(targetEntity="Type")
     * @ORM\JoinColumn(name="type_name", referencedColumnName="name", onDelete="SET NULL")
     * @Soap\ComplexType("string", nillable=true)
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "order"=50,
     *              "short"=true
     *          }
     *      }
     * )
     */
    protected $type;

    /**
     * @var parent
     *
     * @ORM\ManyToOne(targetEntity="Issue", inversedBy="children"))
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     * @Soap\ComplexType("int", nillable=true)
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "order"=110,
     *              "short"=true
     *          }
     *      }
     * )
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Issue", mappedBy="parent")
     * @Soap\ComplexType("int[]", nillable=true)
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     **/
    protected $children;

    /**
     * @var WorkflowItem
     *
     * @ORM\OneToOne(targetEntity="Oro\Bundle\WorkflowBundle\Entity\WorkflowItem")
     * @ORM\JoinColumn(name="workflow_item_id", referencedColumnName="id", onDelete="SET NULL")
     * @Soap\ComplexType("int", nillable=true)
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     */
    protected $workflowItem;

    /**
     * @var WorkflowStep
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\WorkflowBundle\Entity\WorkflowStep")
     * @ORM\JoinColumn(name="workflow_step_id", referencedColumnName="id", onDelete="SET NULL")
     * @Soap\ComplexType("int", nillable=true)
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     */
    protected $workflowStep;

    /**
     * @var ArrayCollection
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     */
    protected $tags;

    public function __construct()
    {
        parent::__construct();

        $this->collaborators = new ArrayCollection();
        $this->related_issues = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getCode().': '.$this->getSummary();
    }

    /**
     * Check if entity is empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->id);
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
     * Update updatedAt.
     *
     * @return Issue
     */
    public function updateUpdatedAt()
    {
        return $this->setUpdatedAt(new \DateTime('now', new \DateTimeZone('UTC')));
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
        $this->updateUpdatedAt();
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

        return $this;
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

        return $this;
    }

    /**
     * Add collaborator.
     *
     * @param User
     *
     * @return Issue
     */
    public function addCollaborator($collaborator)
    {
        $collaborators = $this->getCollaborators();
        if ($collaborator && is_object($collaborators) && !$collaborators->contains($collaborator)) {
            $collaborators->add($collaborator);
        }

        return $this;
    }

    /**
     * Remove collaborator.
     *
     * @param User $collaborators
     *
     * @return Issue
     */
    public function removeCollaborator($collaborator)
    {
        $this->getCollaborators()->removeElement($collaborator);

        return $this;
    }

    /**
     * Get collaborators.
     *
     * @return Collection
     */
    public function getCollaborators()
    {
        if (!$this->collaborators) {
            $this->collaborators = new ArrayCollection();
        }

        return $this->collaborators;
    }

    /**
     * @return Priority
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param Priority $priority
     *
     * @return Issue
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return Resolution
     */
    public function getResolution()
    {
        return $this->resolution;
    }

    /**
     * @param Resolution $resolution
     *
     * @return Issue
     */
    public function setResolution($resolution)
    {
        $this->resolution = $resolution;

        return $this;
    }

    /**
     * Add related_issue.
     *
     * @param Issue $relatedIssue
     *
     * @return Issue
     */
    public function addRelatedIssue($relatedIssue)
    {
        $related_issues = $this->getRelatedIssues();
        if ($relatedIssue && is_object($related_issues) && !$related_issues->contains($relatedIssue)) {
            $related_issues->add($relatedIssue);
        }

        return $this;
    }

    /**
     * Remove related_issue.
     *
     * @param Issue $relatedIssue
     *
     * @return Issue
     */
    public function removeRelatedIssue($relatedIssue)
    {
        $this->getRelatedIssues()->removeElement($relatedIssue);

        return $this;
    }

    /**
     * Get related_issues.
     *
     * @return Collection
     */
    public function getRelatedIssues()
    {
        if (!$this->related_issues) {
            $this->related_issues = new ArrayCollection();
        }

        return $this->related_issues;
    }

    /**
     * @return Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param Type $type
     *
     * @return Issue
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Checks if Issue support children.
     *
     * @return bool
     */
    public function isSupportChildren()
    {
        return $this->getType() && $this->getType()->getChildren()->count();
    }

    /**
     * Set parent.
     *
     * @param Issue $parent
     *
     * @return Issue
     */
    public function setParent($parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent.
     *
     * @return Issue
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children.
     *
     * @param Issue $children
     *
     * @return Issue
     */
    public function addChild($children)
    {
        $childrens = $this->getChildren();
        if ($children && is_object($childrens) && !$childrens->contains($children)) {
            $childrens->add($children);
        }

        return $this;
    }

    /**
     * Remove children.
     *
     * @param Issue $children
     *
     * @return Issue
     */
    public function removeChild($children)
    {
        $this->getChildren()->removeElement($children);

        return $this;
    }

    /**
     * Get children.
     *
     * @return Collection
     */
    public function getChildren()
    {
        if (!$this->children) {
            $this->children = new ArrayCollection();
        }

        return $this->children;
    }

    /**
     * Set workflowItem.
     *
     * @param WorkflowItem $workflowItem
     *
     * @return Issue
     */
    public function setWorkflowItem($workflowItem = null)
    {
        $this->workflowItem = $workflowItem;

        return $this;
    }

    /**
     * Get workflowItem.
     *
     * @return WorkflowItem
     */
    public function getWorkflowItem()
    {
        return $this->workflowItem;
    }

    /**
     * Set workflowStep.
     *
     * @param WorkflowStep $workflowStep
     *
     * @return Issue
     */
    public function setWorkflowStep($workflowStep = null)
    {
        $this->workflowStep = $workflowStep;

        return $this;
    }

    /**
     * Get workflowStep.
     *
     * @return WorkflowStep
     */
    public function getWorkflowStep()
    {
        return $this->workflowStep;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaggableId()
    {
        return $this->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getTags()
    {
        if (!$this->tags) {
            $this->tags = new ArrayCollection();
        }

        return $this->tags;
    }

    /**
     * {@inheritdoc}
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }
}
