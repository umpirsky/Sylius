<?php

namespace Hypebeast\Bundle\CoreBundle\Processor;

use Hypebeast\Bundle\CoreBundle\Entity\GiftCard;
use Hypebeast\Bundle\CoreBundle\Mailer\GiftCardMailer;
use Hypebeast\Bundle\CoreBundle\Builder\GiftCardPromotionBuilder;
use Doctrine\ORM\EntityManagerInterface;

class GiftCardProcessor
{
    protected $mailer;
    protected $promotionBuilder;
    protected $entityManager;

    public function __construct(GiftCardMailer $mailer, GiftCardPromotionBuilder $promotionBuilder, EntityManagerInterface $entityManager)
    {
        $this->mailer = $mailer;
        $this->promotionBuilder = $promotionBuilder;
        $this->entityManager = $entityManager;
    }

    public function send(GiftCard $giftCard)
    {
        $promotion = $this->promotionBuilder->create($giftCard);

        $giftCard->setPromotion($promotion);

        $this->mailer->sendGiftCard($giftCard);

        $giftCard->setStatus($giftCard::STATUS_SENT);

        $this->entityManager->flush($giftCard);
    }
}
