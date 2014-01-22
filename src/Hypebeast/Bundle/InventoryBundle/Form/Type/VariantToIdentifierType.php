<?php

namespace Hypebeast\Bundle\InventoryBundle\Form\Type;

use Hypebeast\Bundle\CoreBundle\Form\Type\IdentifierToEntityType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VariantToIdentifierType extends IdentifierToEntityType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'identifier' => 'id',
                'class'      => 'Sylius\Bundle\CoreBundle\Model\Variant',
            ))
            ->setAllowedTypes(array(
                'identifier' => array('string'),
                'class'      => array('string'),
            ))
        ;
    }

    public function getParent()
    {
        return 'hidden';
    }

    public function getName()
    {
        return 'sylius_variant_to_identifier_hidden';
    }
}
