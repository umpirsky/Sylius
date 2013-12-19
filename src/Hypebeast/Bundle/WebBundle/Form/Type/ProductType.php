<?php

namespace Hypebeast\Bundle\WebBundle\Form\Type;

use Sylius\Bundle\CoreBundle\Form\Type\ProductType as BaseProductType;
use Symfony\Component\Form\FormBuilderInterface;

class ProductType extends BaseProductType
{
    public function buildForm(FormBuilderInterface $builder, array $options = [])
    {
        parent::buildForm($builder, $options);

        $builder->add('upSells', 'entity', [
            'property'      => 'name',
            'class'         => $this->dataClass,
            'query_builder' => null,
            'multiple'      => true,
            'expanded'      => true,
        ])->add('crossSells', 'entity', [
            'property'      => 'name',
            'class'         => $this->dataClass,
            'query_builder' => null,
            'multiple'      => true,
            'expanded'      => true,
        ]);
    }
}
