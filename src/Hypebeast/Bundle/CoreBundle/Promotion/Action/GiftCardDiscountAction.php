<?php

namespace Hypebeast\Bundle\CoreBundle\Promotion\Action;

use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\PromotionsBundle\Action\PromotionActionInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;

class GiftCardDiscountAction implements PromotionActionInterface
{
    protected $adjustmentRepository;
    protected $giftCardRepository;

    public function __construct(RepositoryInterface $adjustmentRepository, RepositoryInterface $giftCardRepository)
    {
        $this->adjustmentRepository = $adjustmentRepository;
        $this->giftCardRepository = $giftCardRepository;
    }

    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        $giftCard = $this->giftCardRepository->findOneBy(['coupon' => $subject->getPromotionCoupon()]);
        if (null === $giftCard) {
            return;
        }

        $adjustment = $this->adjustmentRepository->createNew();

        $adjustment->setAmount(-$giftCard->getValue());
        $adjustment->setLabel(OrderInterface::PROMOTION_ADJUSTMENT);
        $adjustment->setDescription($promotion->getDescription());

        $subject->addAdjustment($adjustment);
    }

    public function getConfigurationFormType()
    {
        return 'sylius_promotion_action_gift_card_discount_configuration';
    }
}
