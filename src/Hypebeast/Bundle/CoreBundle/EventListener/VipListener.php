<?php

namespace Hypebeast\Bundle\CoreBundle\EventListener;

use Symfony\Component\EventDispatcher\GenericEvent;
use Sylius\Bundle\PaymentsBundle\Model\PaymentInterface;
use Hypebeast\Bundle\CoreBundle\Processor\VipProcessor;

class VipListener
{
    private $vipProcessor;

    public function __construct(VipProcessor $vipProcessor)
    {
        $this->vipProcessor = $vipProcessor;
    }

    public function onPaymentPostStateChange(GenericEvent $event)
    {
        $payment = $event->getSubject();

        if (!$payment instanceof PaymentInterface) {
            throw new \InvalidArgumentException(
                'Event subject to be instance of "Sylius\Bundle\PaymentsBundle\Model\PaymentInterface".'
            );
        }

        if ($payment::STATE_COMPLETED !== $payment->getState()) {
            return;
        }

        $this->vipProcessor->process();
    }
}
