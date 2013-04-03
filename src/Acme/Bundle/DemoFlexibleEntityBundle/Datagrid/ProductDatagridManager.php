<?php

namespace Acme\Bundle\DemoFlexibleEntityBundle\Datagrid;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Oro\Bundle\GridBundle\Datagrid\FlexibleDatagridManager;
use Oro\Bundle\GridBundle\Field\FieldDescription;
use Oro\Bundle\GridBundle\Field\FieldDescriptionCollection;
use Oro\Bundle\GridBundle\Field\FieldDescriptionInterface;
use Oro\Bundle\GridBundle\Filter\FilterInterface;
use Oro\Bundle\GridBundle\Action\ActionInterface;
use Oro\Bundle\GridBundle\Property\UrlProperty;
use Oro\Bundle\FlexibleEntityBundle\Model\AbstractAttributeType;

class ProductDatagridManager extends FlexibleDatagridManager
{
    /**
     * @var FieldDescriptionCollection
     */
    protected $fieldsCollection;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var array
     */
    protected static $typeMatches = array(
        AbstractAttributeType::BACKEND_TYPE_DATE => array(
            'field'  => FieldDescriptionInterface::TYPE_DATE,
            'filter' => FilterInterface::TYPE_FLEXIBLE_DATE,
        ),
        AbstractAttributeType::BACKEND_TYPE_DATETIME => array(
            'field'  => FieldDescriptionInterface::TYPE_DATETIME,
            'filter' => FilterInterface::TYPE_FLEXIBLE_STRING,
        ),
        AbstractAttributeType::BACKEND_TYPE_DECIMAL => array(
            'field'  => FieldDescriptionInterface::TYPE_DECIMAL,
            'filter' => FilterInterface::TYPE_FLEXIBLE_NUMBER,
        ),
        AbstractAttributeType::BACKEND_TYPE_INTEGER => array(
            'field'  => FieldDescriptionInterface::TYPE_INTEGER,
            'filter' => FilterInterface::TYPE_FLEXIBLE_NUMBER,
        ),
        AbstractAttributeType::BACKEND_TYPE_OPTION => array(
            'field'  => FieldDescriptionInterface::TYPE_OPTIONS,
            'filter' => FilterInterface::TYPE_FLEXIBLE_OPTIONS,
        ),
        AbstractAttributeType::BACKEND_TYPE_OPTIONS => array(
            'field'  => FieldDescriptionInterface::TYPE_OPTIONS,
            'filter' => FilterInterface::TYPE_FLEXIBLE_OPTIONS,
        ),
        AbstractAttributeType::BACKEND_TYPE_TEXT => array(
            'field'  => FieldDescriptionInterface::TYPE_TEXT,
            'filter' => FilterInterface::TYPE_FLEXIBLE_STRING,
        ),
        AbstractAttributeType::BACKEND_TYPE_VARCHAR => array(
            'field' => FieldDescriptionInterface::TYPE_TEXT,
            'filter' => FilterInterface::TYPE_FLEXIBLE_STRING,
        ),
        AbstractAttributeType::BACKEND_TYPE_PRICE => array(
            'field'  => FieldDescriptionInterface::TYPE_TEXT,
            'filter' => FilterInterface::TYPE_FLEXIBLE_STRING,
        ),
        AbstractAttributeType::BACKEND_TYPE_METRIC => array(
            'field'  => FieldDescriptionInterface::TYPE_TEXT,
            'filter' => FilterInterface::TYPE_FLEXIBLE_STRING,
        ),
    );

    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    protected function getProperties()
    {
        return array(
            new UrlProperty('edit_link', $this->router, 'acme_demoflexibleentity_product_edit', array('id')),
            new UrlProperty('delete_link', $this->router, 'acme_demoflexibleentity_product_remove', array('id')),
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

            $fieldSku = new FieldDescription();
            $fieldSku->setName('sku');
            $fieldSku->setOptions(
                array(
                    'type'        => FieldDescriptionInterface::TYPE_TEXT,
                    'label'       => 'Sku',
                    'field_name'  => 'sku',
                    'filter_type' => FilterInterface::TYPE_STRING,
                    'required'    => false,
                    'sortable'    => true,
                    'filterable'  => true,
                    'show_filter' => true,
                )
            );
            $this->fieldsCollection->add($fieldSku);

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

                if ($attributeType == FieldDescriptionInterface::TYPE_OPTIONS) {
                    $field->setOption('multiple', true);
                }

                // until we support these backend types
                if ($attribute->getCode() === 'price' or $attribute->getCode() === 'size') {
                    $field->setOption('sortable', false);
                    $field->setOption('filterable', false);
                }

                $this->fieldsCollection->add($field);
            }
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

    /**
     * Override to add support for price and metric
     * @param $flexibleFieldType
     * @return string
     * @throws \LogicException
     */
    public function convertFlexibleTypeToFieldType($flexibleFieldType)
    {
        if (!isset(self::$typeMatches[$flexibleFieldType]['field'])) {
            throw new \LogicException('Unknown flexible backend field type.');
        }

        return self::$typeMatches[$flexibleFieldType]['field'];
    }

    /**
     * Override to add support for price and metric
     * @param $flexibleFieldType
     * @return string
     * @throws \LogicException
     */
    public function convertFlexibleTypeToFilterType($flexibleFieldType)
    {
        if (!isset(self::$typeMatches[$flexibleFieldType]['filter'])) {
            throw new \LogicException('Unknown flexible backend filter type.');
        }

        return self::$typeMatches[$flexibleFieldType]['filter'];
    }
}
