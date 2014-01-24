<?php

namespace Hypebeast\Bundle\CoreBundle\Promotion\Action;

use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\PromotionsBundle\Action\PromotionActionInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;
use Sylius\Bundle\ResourceBundle\Exception\UnexpectedTypeException;

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
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, 'Sylius\Bundle\CoreBundle\Model\OrderInterface');
        }

        $giftCard = $this->giftCardRepository->findOneBy(['coupon' => $subject->getPromotionCoupon()]);
        if (null === $giftCard) {
            return;
        }

        $subject->calculateTotal();
        $adjustment = $this->adjustmentRepository->createNew();

        $adjustment->setAmount($subject->getItemsTotal() >= $giftCard->getValue() ? -$giftCard->getValue() : -$subject->getItemsTotal());
        $adjustment->setLabel(OrderInterface::PROMOTION_ADJUSTMENT);
        $adjustment->setDescription($promotion->getDescription());

        $subject->addAdjustment($adjustment);
    }

    public function revert(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        $subject->removePromotionAdjustments();
    }

    public function getConfigurationFormType()
    {
        return 'sylius_promotion_action_no_configuration';
    }
}
