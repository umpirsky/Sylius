<?php

namespace Hypebeast\Bundle\InventoryBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MovementHistoryFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sku', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.inventory_movement_history_filter.sku',
                'attr'     => array(
                    'placeholder' => 'sylius.form.inventory_movement_history_filter.sku'
                )
            ))
            ->add('adjustment', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.inventory_movement_history_filter.adjustment',
                'attr'     => array(
                    'placeholder' => 'sylius.form.inventory_movement_history_filter.adjustment'
                )
            ))
            ->add('number', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.inventory_movement_history_filter.number',
                'attr'     => array(
                    'placeholder' => 'sylius.form.inventory_movement_history_filter.number'
                )
            ))
            ->add('createdAtFrom', 'date', array(
                'required' => false,
                'widget'   => 'single_text',
                'input'    => 'string',
                'label'    => 'sylius.form.inventory_movement_history_filter.created_at_from',
                'attr'     => array(
                    'placeholder' => 'sylius.form.inventory_movement_history_filter.created_at_from'
                )
            ))
            ->add('createdAtTo', 'date', array(
                'required' => false,
                'widget'   => 'single_text',
                'input'    => 'string',
                'label'    => 'sylius.form.inventory_movement_history_filter.created_at_to',
                'attr'     => array(
                    'placeholder' => 'sylius.form.inventory_movement_history_filter.created_at_to'
                )
            ))
        ;
    }

    public function getName()
    {
        return 'sylius_inventory_movement_history_filter';
    }
}
