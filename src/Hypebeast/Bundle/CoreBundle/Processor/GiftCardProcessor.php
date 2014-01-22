<?php

namespace Hypebeast\Bundle\CoreBundle\Processor;

use Hypebeast\Bundle\CoreBundle\Entity\GiftCard;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;
use Hypebeast\Bundle\CoreBundle\Mailer\GiftCardMailer;
use Hypebeast\Bundle\CoreBundle\Builder\GiftCardPromotionBuilder;
use Doctrine\ORM\EntityManagerInterface;

class GiftCardProcessor
{
    protected $mailer;
    protected $entityManager;
    protected $giftCardRepository;

    public function __construct(GiftCardMailer $mailer, EntityManagerInterface $entityManager, RepositoryInterface $giftCardRepository)
    {
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        $this->giftCardRepository = $giftCardRepository;
    }

    public function sendGiftCard(GiftCard $giftCard)
    {
        $this->mailer->sendGiftCard($giftCard);

        $giftCard->setStatus($giftCard::STATUS_SENT);

        $this->entityManager->flush($giftCard);
    }

    public function useGiftCard(OrderInterface $order)
    {
        if (null === $coupon = $order->getPromotionCoupon()) {
            return;
        }

        if (null === $giftCard = $this->giftCardRepository->findOneBy(['coupon' => $coupon])) {
            return;
        }

        foreach ($order->getAdjustments() as $adjustment) {
            if (OrderInterface::PROMOTION_ADJUSTMENT === $adjustment->getLabel()) {
                $giftCard->deductValue(abs($adjustment->getAmount()));
            }
        }
    }
}
