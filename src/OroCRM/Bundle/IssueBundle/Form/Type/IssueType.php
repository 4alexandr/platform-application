<?php

namespace OroCRM\Bundle\IssueBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class IssueType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
}
