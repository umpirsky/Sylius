<?php

namespace Hypebeast\Bundle\InventoryBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdjustmentChangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('typehead', 'text', array(
                'virtual'  => true,
                'label'    => 'sylius.label.adjustment_change.typehead'
            ))
            ->add('variant', 'sylius_variant_to_identifier_hidden', array(
                'label' => 'sylius.label.adjustment_change.variant'
            ))
            ->add('quantity', 'integer', array(
                'label' => 'sylius.label.adjustment_change.quantity'
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => 'Hypebeast\Bundle\InventoryBundle\Entity\AdjustmentChange',
            )
        );
    }

    public function getName()
    {
        return 'sylius_inventory_adjustment_change';
    }
}
