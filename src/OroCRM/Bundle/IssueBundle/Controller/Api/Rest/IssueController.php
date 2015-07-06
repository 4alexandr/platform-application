<?php

namespace OroCRM\Bundle\IssueBundle\Controller\Api\Rest;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Oro\Bundle\SoapBundle\Entity\Manager\ApiEntityManager;
use Oro\Bundle\SoapBundle\Form\Handler\ApiFormHandler;
use Oro\Bundle\SoapBundle\Controller\Api\Rest\RestController;

/**
 * @RouteResource("issue")
 * @NamePrefix("orocrm_api_")
 */
class IssueController extends RestController implements ClassResourceInterface
{
    /**
     * REST GET list.
     *
     * @QueryParam(
     *      name="page",
     *      requirements="\d+",
     *      nullable=true,
     *      description="Page number, starting from 1. Defaults to 1."
     * )
     * @QueryParam(
     *      name="limit",
     *      requirements="\d+",
     *      nullable=true,
     *      description="Number of items per page. defaults to 10."
     * )
     * @ApiDoc(
     *      description="Get all issue items",
     *      resource=true
     * )
     *
     * @return Response
     */
    public function cgetAction()
    {
        $page = (int) $this->getRequest()->get('page', 1);
        $limit = (int) $this->getRequest()->get('limit', self::ITEMS_PER_PAGE);

        return $this->handleGetListRequest($page, $limit);
    }

    /**
     * REST GET item.
     *
     * @param string $id
     *
     * @ApiDoc(
     *      description="Get issue item",
     *      resource=true
     * )
     *
     * @return Response
     */
    public function getAction($id)
    {
        return $this->handleGetRequest($id);
    }

    /**
     * REST PUT.
     *
     * @param int $id Issue item id
     *
     * @ApiDoc(
     *      description="Update issue",
     *      resource=true
     * )
     *
     * @return Response
     */
    public function putAction($id)
    {
        return $this->handleUpdateRequest($id);
    }

    /**
     * Create new issue.
     *
     * @ApiDoc(
     *      description="Create new issue",
     *      resource=true
     * )
     */
    public function postAction()
    {
        return $this->handleCreateRequest();
    }

    /**
     * {@inheritdoc}
     */
    protected function createEntity()
    {
        $entity = parent::createEntity();

        $parent = (int) $this->getRequest()->request->get('parent');
        if ($parent) {
            $parent = $this->getManager()->find($parent);
            if ($parent && $parent->isSupportChildren()) {
                $entity->setParent($parent);
            }
        }

        return $entity;
    }

    /**
     * REST DELETE.
     *
     * @param int $id
     *
     * @ApiDoc(
     *      description="Delete Issue",
     *      resource=true
     * )
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        return $this->handleDeleteRequest($id);
    }

    /**
     * Get entity Manager.
     *
     * @return ApiEntityManager
     */
    public function getManager()
    {
        return $this->get('orocrm_issue.manager.api');
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->get('orocrm_issue.form.api');
    }

    /**
     * @return ApiFormHandler
     */
    public function getFormHandler()
    {
        return $this->get('orocrm_issue.form.handler.issue_api');
    }

    /**
     * {@inheritdoc}
     */
    protected function transformEntityField($field, &$value)
    {
        switch ($field) {
            case 'priority':
            case 'resolution':
            case 'type':
                if ($value) {
                    $value = $value->getName();
                }
                break;
            case 'parent':
            case 'owner':
            case 'reporter':
            case 'workflowItem':
            case 'workflowStep':
                if ($value) {
                    $value = $value->getId();
                }
                break;
            case 'collaborators':
            case 'children':
                $value = $value->map(function ($item) {
                    return $item->getId();
                })->toArray();
                break;
            default:
                parent::transformEntityField($field, $value);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function fixFormData(array &$data, $entity)
    {
        parent::fixFormData($data, $entity);

        unset($data['parent']);

        return true;
    }
}
