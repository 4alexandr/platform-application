<?php

namespace OroCRM\Bundle\IssueBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Oro\Bundle\SoapBundle\Form\EventListener\PatchSubscriber;

class IssueApiType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'createdAt',
            'oro_datetime',
            [
                'required' => false,
            ]
        );

        $builder->addEventSubscriber(new PatchSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'csrf_protection' => false,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'orocrm_issue';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'issue';
    }
}
