<?php

namespace Hypebeast\Bundle\WebBundle\Form\Type;

use Sylius\Bundle\AddressingBundle\Form\Type\AddressType as BaseType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddressType extends BaseType
{
    protected $container;

    public function __construct(ContainerInterface $container, $dataClass, array $validationGroups, EventSubscriberInterface $eventListener)
    {
        $this->container = $container;

        parent::__construct($dataClass, $validationGroups, $eventListener);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $session = $this->getRequest()->getSession();

        $builder->get('country')->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($session) {
            // Only set default country if no data is set.
            if (null === $event->getData()) {
                $event->setData($session->get('_hypebeast_default_country'));
            }
        });
    }

    protected function getRequest()
    {
        return $this->container->get('request');
    }
}
