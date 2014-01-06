<?php

namespace Hypebeast\Bundle\WebBundle\Form\Type;

use Sylius\Bundle\CoreBundle\Form\Type\ShippingMethodType as BaseType;
use Symfony\Component\Form\FormBuilderInterface;

class ShippingMethodType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('estimationDaysFrom', 'number', array(
                'label' => 'sylius.form.shipping_method.estimation_from'
            ));

        $builder->add('estimationDaysTo', 'number', array(
                'label' => 'sylius.form.shipping_method.estimation_to'
            ));
    }
}
