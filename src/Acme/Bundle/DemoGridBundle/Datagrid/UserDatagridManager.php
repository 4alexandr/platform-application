<?php

namespace Acme\Bundle\DemoGridBundle\Datagrid;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Oro\Bundle\GridBundle\Datagrid\FlexibleDatagridManager;
use Oro\Bundle\GridBundle\Field\FieldDescription;
use Oro\Bundle\GridBundle\Field\FieldDescriptionCollection;
use Oro\Bundle\GridBundle\Field\FieldDescriptionInterface;
use Oro\Bundle\GridBundle\Filter\FilterInterface;
use Oro\Bundle\GridBundle\Action\ActionInterface;
use Oro\Bundle\GridBundle\Property\UrlProperty;

class UserDatagridManager extends FlexibleDatagridManager
{
    /**
     * @var FieldDescriptionCollection
     */
    protected $fieldsCollection;

    /**
     * @var Router
     */
    protected $router;

    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    protected function getProperties()
    {
        return array(
            new UrlProperty('edit_link', $this->router, 'oro_user_edit', array('id')),
            new UrlProperty('delete_link', $this->router, 'oro_api_delete_profile', array('id')),
        );
    }

    /**
     * @return FieldDescriptionCollection
     */
    protected function getFieldDescriptionCollection()
    {
        if (!$this->fieldsCollection) {
            $this->fieldsCollection = new FieldDescriptionCollection();

            $fieldId = new FieldDescription();
            $fieldId->setName('id');
            $fieldId->setOptions(
                array(
                    'type'        => FieldDescriptionInterface::TYPE_INTEGER,
                    'label'       => 'ID',
                    'field_name'  => 'id',
                    'filter_type' => FilterInterface::TYPE_NUMBER,
                    'required'    => false,
                    'sortable'    => true,
                    'filterable'  => true,
                    'show_filter' => true,
                )
            );
            $this->fieldsCollection->add($fieldId);

            $fieldUsername = new FieldDescription();
            $fieldUsername->setName('username');
            $fieldUsername->setOptions(
                array(
                    'type'        => FieldDescriptionInterface::TYPE_TEXT,
                    'label'       => 'Username',
                    'field_name'  => 'username',
                    'filter_type' => FilterInterface::TYPE_STRING,
                    'required'    => false,
                    'sortable'    => true,
                    'filterable'  => true,
                    'show_filter' => true,
                )
            );
            $this->fieldsCollection->add($fieldUsername);

            $fieldEmail = new FieldDescription();
            $fieldEmail->setName('email');
            $fieldEmail->setOptions(
                array(
                    'type'        => FieldDescriptionInterface::TYPE_TEXT,
                    'label'       => 'Email',
                    'field_name'  => 'email',
                    'filter_type' => FilterInterface::TYPE_STRING,
                    'required'    => false,
                    'sortable'    => true,
                    'filterable'  => true,
                    'show_filter' => true,
                )
            );
            $this->fieldsCollection->add($fieldEmail);

            $fieldFirstName = new FieldDescription();
            $fieldFirstName->setName('firstName');
            $fieldFirstName->setOptions(
                array(
                    'type'        => FieldDescriptionInterface::TYPE_TEXT,
                    'label'       => 'First name',
                    'field_name'  => 'firstName',
                    'filter_type' => FilterInterface::TYPE_STRING,
                    'required'    => false,
                    'sortable'    => true,
                    'filterable'  => true,
                    'show_filter' => true,
                )
            );
            $this->fieldsCollection->add($fieldFirstName);

            $fieldLastName = new FieldDescription();
            $fieldLastName->setName('lastName');
            $fieldLastName->setOptions(
                array(
                    'type'        => FieldDescriptionInterface::TYPE_TEXT,
                    'label'       => 'Last name',
                    'field_name'  => 'lastName',
                    'filter_type' => FilterInterface::TYPE_STRING,
                    'required'    => false,
                    'sortable'    => true,
                    'filterable'  => true,
                    'show_filter' => true,
                )
            );
            $this->fieldsCollection->add($fieldLastName);

            $fieldBirthday = new FieldDescription();
            $fieldBirthday->setName('birthday');
            $fieldBirthday->setOptions(
                array(
                    'type'        => FieldDescriptionInterface::TYPE_DATE,
                    'label'       => 'Birthday',
                    'field_name'  => 'birthday',
                    'filter_type' => FilterInterface::TYPE_DATE,
                    'required'    => false,
                    'sortable'    => true,
                    'filterable'  => true,
                    'show_filter' => true,
                )
            );
            $this->fieldsCollection->add($fieldBirthday);

            foreach ($this->getFlexibleAttributes() as $attribute) {
                $backendType   = $attribute->getBackendType();
                $attributeType = $this->convertFlexibleTypeToFieldType($backendType);
                $filterType    = $this->convertFlexibleTypeToFilterType($backendType);

                $field = new FieldDescription();
                $field->setName($attribute->getCode());
                $field->setOptions(
                    array(
                        'type'          => $attributeType,
                        'label'         => $attribute->getCode(),
                        'field_name'    => $attribute->getCode(),
                        'filter_type'   => $filterType,
                        'required'      => false,
                        'sortable'      => true,
                        'filterable'    => true,
                        'flexible_name' => $this->flexibleManager->getFlexibleName()
                    )
                );

                if ($attributeType == FieldDescriptionInterface::TYPE_OPTIONS
                    && $attribute->getCode() == 'hobby'
                ) {
                    $field->setOption('multiple', true);
                }

                $this->fieldsCollection->add($field);
            }

            $fieldCreated = new FieldDescription();
            $fieldCreated->setName('created');
            $fieldCreated->setOptions(
                array(
                    'type'        => FieldDescriptionInterface::TYPE_DATETIME,
                    'label'       => 'Created At',
                    'field_name'  => 'created',
                    'filter_type' => FilterInterface::TYPE_DATETIME,
                    'required'    => false,
                    'sortable'    => true,
                    'filterable'  => true,
                    'show_filter' => true,
                )
            );
            $this->fieldsCollection->add($fieldCreated);

            $fieldUpdated = new FieldDescription();
            $fieldUpdated->setName('updated');
            $fieldUpdated->setOptions(
                array(
                    'type'        => FieldDescriptionInterface::TYPE_DATETIME,
                    'label'       => 'Updated At',
                    'field_name'  => 'updated',
                    'filter_type' => FilterInterface::TYPE_DATETIME,
                    'required'    => false,
                    'sortable'    => true,
                    'filterable'  => true,
                    'show_filter' => true,
                )
            );
            $this->fieldsCollection->add($fieldUpdated);
        }

        return $this->fieldsCollection;
    }

    /**
     * {@inheritdoc}
     */
    protected function getListFields()
    {
        return $this->getFieldDescriptionCollection()->getElements();
    }

    /**
     * {@inheritdoc}
     */
    protected function getSorters()
    {
        $fields = array();
        /** @var $fieldDescription FieldDescription */
        foreach ($this->getFieldDescriptionCollection() as $fieldDescription) {
            if ($fieldDescription->isSortable()) {
                $fields[] = $fieldDescription;
            }
        }

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        $fields = array();
        /** @var $fieldDescription FieldDescription */
        foreach ($this->getFieldDescriptionCollection() as $fieldDescription) {
            if ($fieldDescription->isFilterable()) {
                $fields[] = $fieldDescription;
            }
        }

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    protected function getRowActions()
    {
        $editAction = array(
            'name'         => 'edit',
            'type'         => ActionInterface::TYPE_REDIRECT,
            'acl_resource' => 'root',
            'options'      => array(
                'label'=> 'Edit',
                'icon' => 'edit',
                'link' => 'edit_link',
                'backUrl' => true,
            )
        );

        $deleteAction = array(
            'name'         => 'delete',
            'type'         => ActionInterface::TYPE_DELETE,
            'acl_resource' => 'root',
            'options'      => array(
                'label'=> 'Delete',
                'icon' => 'trash',
                'link' => 'delete_link',
            )
        );

        return array($editAction, $deleteAction);
    }
}
