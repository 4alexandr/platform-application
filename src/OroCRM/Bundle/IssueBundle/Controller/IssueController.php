<?php

namespace OroCRM\Bundle\IssueBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use OroCRM\Bundle\IssueBundle\Entity\Issue;
use Oro\Bundle\UserBundle\Entity\User;

/**
 * @Route("/issue")
 */
class IssueController extends Controller
{
    /**
     * @Route(
     *      ".{_format}",
     *      name="orocrm_issue_index",
     *      requirements={"_format"="html|json"},
     *      defaults={"_format" = "html"}
     * )
     * @Template
     */
    public function indexAction()
    {
        return [
            'entity_class' => $this->container->getParameter('orocrm_issue.entity.class'),
        ];
    }

    /**
     * @Route("/view/{id}", name="orocrm_issue_view", requirements={"id"="\d+"})
     * @Template
     */
    public function viewAction(Issue $issue)
    {
        return ['entity' => $issue];
    }

    /**
     * @Route("/create", name="orocrm_issue_create")
     * @Template("OroCRMIssueBundle:Issue:update.html.twig")
     */
    public function createAction()
    {
        $issue = new Issue();

        return $this->update($issue);
    }

    /**
     * @Route("/create/subtask/{id}", name="orocrm_issue_create_children", requirements={"id"="\d+"})
     * @Template("OroCRMIssueBundle:Issue:update.html.twig")
     */
    public function createSubtaskAction(Issue $parentIssue)
    {
        if (!$parentIssue->isSupportChildren()) {
            throw $this->createNotFoundException();
        }

        $issue = new Issue();
        $issue->setParent($parentIssue);

        return $this->update($issue);
    }

    /**
     * @Route("/create/assign/{id}", name="orocrm_issue_create_assign", requirements={"id"="\d+"})
     * @Template("OroCRMIssueBundle:Issue:update.html.twig")
     */
    public function createAssignAction(User $owner)
    {
        $issue = new Issue();
        $issue->setOwner($owner);

        return $this->update($issue);
    }

    /**
     * @Route("/update/{id}", name="orocrm_issue_update", requirements={"id"="\d+"})
     * @Template
     */
    public function updateAction(Issue $issue)
    {
        return $this->update($issue);
    }

    /**
     * @param Issue  $issue
     * @param string $formAction
     */
    protected function update(Issue $issue)
    {
        $formAction = $this->get('router')->generate($this->getRequest()->get('_route'), $this->getRequest()->get('_route_params'));

        $saved = $this->get('orocrm_issue.form.handler.issue')->process($issue);

        if ($saved && !$this->getRequest()->get('_widgetContainer')) {
            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('orocrm.issue.saved_message')
            );

            return $this->get('oro_ui.router')->redirectAfterSave(
                [
                    'route' => 'orocrm_issue_update',
                    'parameters' => ['id' => $issue->getId()],
                ],
                [
                    'route' => 'orocrm_issue_view',
                    'parameters' => ['id' => $issue->getId()],
                ]
            );
        }

        return [
            'entity' => $issue,
            'saved' => $saved,
            'form' => $this->get('orocrm_issue.form.handler.issue')->getForm()->createView(),
            'formAction' => $formAction,
        ];
    }
}
