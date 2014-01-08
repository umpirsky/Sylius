<?php

namespace Hypebeast\Bundle\WebBundle\Form\Type\Filter;

use Symfony\Component\Form\FormBuilderInterface;
use Sylius\Bundle\CoreBundle\Form\Type\Filter\OrderFilterType as BaseType;

class OrderFilterType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('orderState', 'hidden')
        ;
    }

    public function getName()
    {
        return 'sylius_order_filter';
    }
}
