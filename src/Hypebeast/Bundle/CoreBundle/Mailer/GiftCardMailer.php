<?php

namespace Hypebeast\Bundle\CoreBundle\Mailer;

use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\CoreBundle\Mailer\TwigMailerInterface;
use Hypebeast\Bundle\CoreBundle\Entity\GiftCard;

class GiftCardMailer
{
    protected $mailer;
    protected $parameters;

    public function __construct(TwigMailerInterface $mailer, array $parameters)
    {
        $this->mailer = $mailer;
        $this->parameters = $parameters;
    }

    public function sendGiftCard(GiftCard $giftCard)
    {
        $this->mailer->sendEmail(
            $this->parameters['template'],
            ['giftCard' => $giftCard],
            $this->parameters['from_email'],
            $giftCard->getEmail()
        );
    }
}
