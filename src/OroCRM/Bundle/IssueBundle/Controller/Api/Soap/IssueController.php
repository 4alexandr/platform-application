<?php

namespace OroCRM\Bundle\IssueBundle\Controller\Api\Soap;

use Symfony\Component\Form\FormInterface;
use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Oro\Bundle\SoapBundle\Controller\Api\Soap\SoapController;
use Oro\Bundle\SoapBundle\Entity\Manager\ApiEntityManager;
use Oro\Bundle\SoapBundle\Form\Handler\ApiFormHandler;

class IssueController extends SoapController
{
    /**
     * @Soap\Method("getIssues")
     * @Soap\Param("page", phpType="int")
     * @Soap\Param("limit", phpType="int")
     * @Soap\Result(phpType = "OroCRM\Bundle\IssueBundle\Entity\IssueSoap[]")
     * @AclAncestor("orocrm_issue_view")
     */
    public function cgetAction($page = 1, $limit = 10)
    {
        return $this->handleGetListRequest($page, $limit);
    }

    /**
     * @Soap\Method("getIssue")
     * @Soap\Param("id", phpType = "int")
     * @Soap\Result(phpType = "OroCRM\Bundle\IssueBundle\Entity\IssueSoap")
     * @AclAncestor("orocrm_issue_view")
     */
    public function getAction($id)
    {
        return $this->handleGetRequest($id);
    }

    /**
     * @Soap\Method("createIssue")
     * @Soap\Param("issue", phpType = "OroCRM\Bundle\IssueBundle\Entity\IssueSoap")
     * @Soap\Result(phpType = "int")
     * @AclAncestor("orocrm_issue_create")
     */
    public function createAction($issue)
    {
        return $this->handleCreateRequest($issue);
    }

    /**
     * {@inheritdoc}
     */
    protected function createEntity()
    {
        $issue = func_get_arg(0);
        $entity = parent::createEntity();

        $parent = (int) $issue->getParent();
        if ($parent) {
            $parent = $this->getManager()->find($parent);
            if ($parent && $parent->isSupportChildren()) {
                $entity->setParent($parent);
            }
        }

        return $entity;
    }

    /**
     * @Soap\Method("updateIssue")
     * @Soap\Param("id", phpType = "int")
     * @Soap\Param("issue", phpType = "OroCRM\Bundle\IssueBundle\Entity\IssueSoap")
     * @Soap\Result(phpType = "boolean")
     * @AclAncestor("orocrm_issue_update")
     */
    public function updateAction($id, $issue)
    {
        return $this->handleUpdateRequest($id);
    }

    /**
     * @Soap\Method("deleteIssue")
     * @Soap\Param("id", phpType = "int")
     * @Soap\Result(phpType = "boolean")
     * @AclAncestor("orocrm_issue_delete")
     */
    public function deleteAction($id)
    {
        return $this->handleDeleteRequest($id);
    }

    /**
     * @return ApiEntityManager
     */
    public function getManager()
    {
        return $this->container->get('orocrm_issue.manager.api');
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->container->get('orocrm_issue.form.api.soap');
    }

    /**
     * @return ApiFormHandler
     */
    public function getFormHandler()
    {
        return $this->container->get('orocrm_issue.form.handler.issue_api.soap');
    }

    /**
     * {@inheritDoc}
     */
    protected function fixFormData(array &$data, $entity)
    {
        parent::fixFormData($data, $entity);

        unset($data['id']);
        unset($data['updatedAt']);
        unset($data['collaborators']);
        unset($data['children']);
        unset($data['parent']);

        return true;
    }

    /*
     * {@inheritDoc}
     */
    /*protected function fixRequestAttributes($entity)
    {
        parent::fixRequestAttributes($entity);
        / *$request = $this->container->get('request');
        $entityData = $request->get($this->getForm()->getName());
        if (!is_object($entityData)) {
            return;
        }

        $data = $this->convertValueToArray($entityData);
        $this->fixFormData($data, $entity);
        $request->request->set($this->getForm()->getName(), $data);* /
    }*/
}
