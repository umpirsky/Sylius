<?php

namespace Hypebeast\Bundle\WebBundle\Form\Type;

use Sylius\Bundle\ProductBundle\Form\Type\PropertyType as BaseType;
use Hypebeast\Bundle\WebBundle\Model\PropertyTypes;
use Symfony\Component\Form\FormBuilderInterface;

class PropertyType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('type', 'choice', array(
                'choices' => PropertyTypes::getChoices()
            ))
        ;
    }
}
