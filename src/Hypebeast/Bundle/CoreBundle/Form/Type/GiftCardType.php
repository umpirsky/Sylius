<?php

namespace Hypebeast\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

class GiftCardType extends GiftCardFrontType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('value', 'sylius_money', array(
                'label' => 'sylius.form.gift_card.value',
            ))
            ->add('coupon', 'entity', array(
                'class'    => 'Sylius\Bundle\PromotionsBundle\Model\Coupon',
                'property' => 'code',
                'label'    => 'sylius.form.gift_card.coupon',
            ))
        ;
    }

    public function getName()
    {
        return 'sylius_gift_card';
    }
}
