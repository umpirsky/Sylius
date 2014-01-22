<?php

namespace Hypebeast\Bundle\CoreBundle\Form\DataTransformer;

use Sylius\Bundle\ResourceBundle\Form\DataTransformer\EntityToIdentifierTransformer;

class IdentifierToEntityTransformer extends EntityToIdentifierTransformer
{
    public function transform($entity)
    {
        return parent::reverseTransform($entity);
    }

    public function reverseTransform($value)
    {
        return parent::transform($value);
    }
}
