<?php

namespace Hypebeast\Bundle\WebBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Sylius\Bundle\ProductBundle\Form\Type\ProductPropertyType as BaseProductPropertyType;
use Hypebeast\Bundle\WebBundle\Form\EventListener\BuildProductPropertyFormListener;

class ProductPropertyType extends BaseProductPropertyType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('property', 'sylius_property_choice')
            ->addEventSubscriber(new BuildProductPropertyFormListener($builder->getFormFactory()))
        ;

        $prototypes = array();
        foreach ($this->getProperties($builder) as $property) {
            $prototypes[] = $builder->create('value', $property->getType(), $property->getConfiguration())->getForm();
        }

        $builder->setAttribute('prototypes', $prototypes);
    }

    private function getProperties(FormBuilderInterface $builder)
    {
        return $builder->get('property')->getOption('choice_list')->getChoices();
    }
}
