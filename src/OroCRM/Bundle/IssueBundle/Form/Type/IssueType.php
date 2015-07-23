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
            'oro_user_select',
            [
                'label' => 'orocrm.issue.reporter.label',
                'required' => false,
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
                'type' => 'orocrm_issue_select',
                'options' => [
                    'label' => 'orocrm.issue.related_issues.label',
                ],
                'required' => false,
            ]
        );

        $builder->add(
            'tags',
            'oro_tag_select',
            [
                'label' => 'oro.tag.entity_plural_label',
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
