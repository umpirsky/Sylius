<?php

namespace Hypebeast\Bundle\WebBundle\Form\Type;

use Sylius\Bundle\AddressingBundle\Form\Type\AddressType as BaseType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

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

        $builder
            ->add('country', 'sylius_country_choice', array(
                'label' => 'sylius.form.address.country',
                'data'  => $session->get('_hypebeast_default_country'),
            ))
        ;
    }

    protected function getRequest()
    {
        return $this->container->get('request');
    }
}
