<?php

namespace Hypebeast\Bundle\InventoryBundle\Form\Type;

use Sylius\Bundle\VariableProductBundle\Form\Type\VariantToIdentifierType as BaseVariantToIdentifierType;

class VariantToIdentifierType extends BaseVariantToIdentifierType
{
    public function getParent()
    {
        return 'hidden';
    }

    public function getName()
    {
        return 'sylius_variant_to_identifier_hidden';
    }
}
