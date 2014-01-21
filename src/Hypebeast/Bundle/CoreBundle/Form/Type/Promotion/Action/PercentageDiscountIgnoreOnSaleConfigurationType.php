<?php

namespace Hypebeast\Bundle\CoreBundle\Form\Type\Promotion\Action;

use Sylius\Bundle\PromotionsBundle\Form\Type\Action\PercentageDiscountConfigurationType;

class PercentageDiscountIgnoreOnSaleConfigurationType extends PercentageDiscountConfigurationType
{
    public function getName()
    {
        return 'sylius_promotion_action_percentage_discount_ignore_on_sale_configuration';
    }
}
