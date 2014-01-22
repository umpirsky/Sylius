<?php

namespace Hypebeast\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Sylius\Bundle\ResourceBundle\Form\Type\EntityToIdentifierType;
use Hypebeast\Bundle\CoreBundle\Form\DataTransformer\IdentifierToEntityTransformer;

class IdentifierToEntityType extends EntityToIdentifierType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(
            new IdentifierToEntityTransformer($this->om->getRepository($options['class']), $options['identifier'])
        );
    }

    public function getName()
    {
        return 'sylius_identifier_to_entity';
    }
}
