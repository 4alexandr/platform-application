<?php

namespace OroCRM\Bundle\IssueBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class IssueType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'preSetData']);

        $builder
            ->add(
                'code',
                'text',
                [
                    'required' => true,
                    'label' => 'orocrm.issue.code.label',
                ]
            )
            ->add(
                'summary',
                'text',
                [
                    'required' => true,
                    'label' => 'orocrm.issue.summary.label',
                ]
            )
            ->add(
                'description',
                'textarea',
                [
                    'required' => false,
                    'label' => 'orocrm.issue.description.label',
                ]
            );

        $builder->add(
            'reporter',
            'oro_user_acl_select',
            [
                'label' => 'orocrm.issue.reporter.label',
                'required' => false,
                'autocomplete_alias' => 'acl_users',
                'configs' => [
                    'placeholder' => 'oro.user.form.choose_user',
                    'result_template_twig' => 'OroUserBundle:User:Autocomplete/result.html.twig',
                    'selection_template_twig' => 'OroUserBundle:User:Autocomplete/selection.html.twig',
                    'extra_config' => 'acl_user_autocomplete',
                    'entity_name' => 'Oro\Bundle\UserBundle\Entity\User',
                    'permission' => 'ASSIGN',
                    'entity_id' => 0,
                ],
            ]
        );

        $builder->add(
            'priority',
            'translatable_entity',
            [
                'label' => 'orocrm.issue.priority.label',
                'class' => 'OroCRM\Bundle\IssueBundle\Entity\Priority',
                'required' => true,
                'query_builder' => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('priority')->orderBy('priority.order');
                },
            ]
        );

        $builder->add(
            'resolution',
            'translatable_entity',
            [
                'label' => 'orocrm.issue.resolution.label',
                'class' => 'OroCRM\Bundle\IssueBundle\Entity\Resolution',
                'required' => false,
            ]
        );

        $builder->add(
            'related_issues',
            'oro_collection',
            [
                'handle_primary' => false,
                'label' => 'orocrm.issue.related_issues.label',
                'type' => 'entity',
                'options' => [
                    'class' => 'OroCRM\Bundle\IssueBundle\Entity\Issue',
                    'empty_value' => '',
                ],
                'required' => false,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'OroCRM\Bundle\IssueBundle\Entity\Issue',
                'intention' => 'issue',
                'cascade_validation' => true,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'orocrm_issue';
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $entity = $event->getData();

        if ($entity === null) {
            return;
        }

        if ($entity->getId()) {
            return;
        }

        $form->add(
            'type',
            'translatable_entity',
            [
                'label' => 'orocrm.issue.type.label',
                'class' => 'OroCRM\Bundle\IssueBundle\Entity\Type',
                'required' => true,
                'query_builder' => function (EntityRepository $repository) use ($entity) {
                    if ($entity->getParent()) {
                        return $repository->createQueryBuilder('type')
                            ->where('type.parent = :parent')
                            ->setParameter('parent', $entity->getParent()->getType()->getName());
                    } else {
                        return $repository->createQueryBuilder('type')
                            ->where('type.parent IS NULL');
                    }
                },
            ]
        );
    }
}
