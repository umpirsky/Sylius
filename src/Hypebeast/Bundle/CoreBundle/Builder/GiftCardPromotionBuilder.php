<?php

namespace Hypebeast\Bundle\CoreBundle\Builder;

use Hypebeast\Bundle\CoreBundle\Entity\GiftCard;

class GiftCardPromotionBuilder
{
    protected $builder;

    public function __construct(PromotionBuilder $builder)
    {
        $this->builder = $builder;
    }

    public function create(GiftCard $giftCard)
    {
        $name = sprintf(
            'Gift card for %s <%s>',
            $giftCard->getName(),
            $giftCard->getEmail()
        );
        $usageLimit = 1;

        return $this->builder
            ->create($name)
            ->setDescription($name)
            ->setCouponBased(true)
            ->setUsageLimit($usageLimit)
            ->addCoupon(uniqid(), $usageLimit)
            ->addAction('fixed_discount', ['amount' => $giftCard->getValue()])
            ->save()
        ;
    }
}
