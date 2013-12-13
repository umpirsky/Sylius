<?php

namespace Hypebeast\Bundle\InventoryBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdjustmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reason', 'text', array(
                'label' => 'sylius.label.adjustment.reason'
            ))
            ->add('note', 'textarea', array(
                'required' => false,
                'label'    => 'sylius.label.adjustment.note'
            ))
            ->add('adjustmentChanges', 'collection', array(
                'type'         => 'sylius_inventory_adjustment_change',
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => 'Hypebeast\Bundle\InventoryBundle\Entity\Adjustment',
            )
        );
    }

    public function getName()
    {
        return 'sylius_inventory_adjustment';
    }
}
