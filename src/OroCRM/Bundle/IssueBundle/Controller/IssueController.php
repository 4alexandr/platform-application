<?php

namespace OroCRM\Bundle\IssueBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use OroCRM\Bundle\IssueBundle\Entity\Issue;

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

        $defaultPriority = $this->getRepository('OroCRMIssueBundle:Priority')->find('major');
        if ($defaultPriority) {
            $issue->setPriority($defaultPriority);
        }

        $defaultType = $this->getRepository('OroCRMIssueBundle:Type')->find('bug');
        if ($defaultType) {
            $issue->setType($defaultType);
        }

        $formAction = $this->get('router')->generate('orocrm_issue_create');

        return $this->update($issue, $formAction);
    }

    /**
     * @Route("/update/{id}/children", name="orocrm_issue_create_children", requirements={"id"="\d+"})
     * @Template("OroCRMIssueBundle:Issue:update.html.twig")
     */
    public function createChildrenAction(Issue $parentIssue)
    {
        if (!$parentIssue->isSupportChildren()) {
            throw $this->createNotFoundException();
        }

        $issue = new Issue();
        $issue->setParent($parentIssue);

        $defaultPriority = $this->getRepository('OroCRMIssueBundle:Priority')->find('major');
        if ($defaultPriority) {
            $issue->setPriority($defaultPriority);
        }

        $defaultType = $this->getRepository('OroCRMIssueBundle:Type')->find('bug');
        if ($defaultType) {
            $issue->setType($defaultType);
        }

        $formAction = $this->get('router')->generate('orocrm_issue_create_children', ['id' => $parentIssue->getId()]);

        return $this->update($issue, $formAction);
    }

    /**
     * @Route("/update/{id}", name="orocrm_issue_update", requirements={"id"="\d+"})
     * @Template
     */
    public function updateAction(Issue $issue)
    {
        $formAction = $this->get('router')->generate('orocrm_issue_update', ['id' => $issue->getId()]);

        return $this->update($issue, $formAction);
    }

    /**
     * @Route("/delete/{id}", name="orocrm_issue_delete", requirements={"id"="\d+"}, methods="DELETE")
     * @Template
     */
    public function deleteAction(Issue $issue)
    {
        $this->getDoctrine()->getManager()->remove($issue);
        $this->getDoctrine()->getManager()->flush($issue);

        return $this->redirect($this->generateUrl('orocrm_issue_index'));
    }

    /**
     * @param Issue  $issue
     * @param string $formAction
     */
    protected function update(Issue $issue, $formAction)
    {
        $saved = false;
        if ($this->get('orocrm_issue.form.handler.issue')->process($issue)) {
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

    /**
     * @param string $entityName
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getRepository($entityName)
    {
        return $this->getDoctrine()->getRepository($entityName);
    }
}
