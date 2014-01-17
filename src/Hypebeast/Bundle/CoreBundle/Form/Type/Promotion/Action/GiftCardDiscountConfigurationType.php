<?php

namespace Hypebeast\Bundle\CoreBundle\Form\Type\Promotion\Action;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class GiftCardDiscountConfigurationType extends AbstractType
{
    public function getName()
    {
        return 'sylius_promotion_action_gift_card_discount_configuration';
    }
}
