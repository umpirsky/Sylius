<?php

namespace Hypebeast\Bundle\WebBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Sylius\Bundle\ProductBundle\Form\EventListener\BuildProductPropertyFormListener as BaseBuildProductPropertyFormListener;

class BuildProductPropertyFormListener extends BaseBuildProductPropertyFormListener
{
    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;

        parent::__construct($factory);
    }

    public function buildForm(FormEvent $event)
    {
        $productProperty = $event->getData();
        $form = $event->getForm();

        if (null === $productProperty) {
            $form->add($this->factory->createNamed('value', 'textarea', null, array('auto_initialize' => false)));

            return;
        }

        $options = array('label' => $productProperty->getName(), 'auto_initialize' => false);

        if (is_array($productProperty->getConfiguration())) {
            $options = array_merge($options, $productProperty->getConfiguration());
        }

        // If we're editing the product property, let's just render the value field, not full selection.
        $form
            ->remove('property')
            ->add($this->factory->createNamed('value', $productProperty->getType(), null, $options))
        ;
    }
}
