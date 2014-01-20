<?php

namespace Hypebeast\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GiftCardFrontType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label'    => 'sylius.form.gift_card.name',
                'required' => false
            ))
            ->add('email', 'email', array(
                'label' => 'sylius.form.gift_card.email'
            ))
            ->add('message', 'textarea', array(
                'label'    => 'sylius.form.gift_card.message',
                'required' => false
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => 'Hypebeast\Bundle\CoreBundle\Entity\GiftCard',
            )
        );
    }

    public function getName()
    {
        return 'sylius_gift_card_front';
    }
}
