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
            ->add('reason', 'choice', array(
                'label' => 'sylius.label.adjustment.reason',
                'choices' => [
                    'New Products' => 'sylius.choice.adjustment.reason.new_product',
                    'Restocked' => 'sylius.choice.adjustment.reason.restocked',
                    'Returned' => 'sylius.choice.adjustment.reason.returned',
                    'Damaged' => 'sylius.choice.adjustment.reason.damaged',
                    'Borrowed for Shooting' => 'sylius.choice.adjustment.reason.borrowed_from_shooting',
                    'Returned from Shooting' => 'sylius.choice.adjustment.reason.returned_from_shooting',
                    'Other' => 'sylius.choice.adjustment.reason.other',
                ],
                'empty_value' => 'Choose a Reason',
                'required' => true
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
