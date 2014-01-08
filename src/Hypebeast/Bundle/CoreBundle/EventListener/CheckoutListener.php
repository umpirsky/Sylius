<?php

namespace Hypebeast\Bundle\CoreBundle\EventListener;

use Symfony\Component\EventDispatcher\GenericEvent;
use Sylius\Bundle\PaymentsBundle\Model\PaymentInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Hypebeast\Bundle\OrderBundle\Status\Processor;
use Doctrine\ORM\EntityManager;

class CheckoutListener
{
    protected $processor;
    protected $em;
    protected $orderRepository;

    public function __construct(Processor $processor, EntityManager $em, EntityRepository $orderRepository)
    {
        $this->processor = $processor;
        $this->em = $em;
        $this->orderRepository = $orderRepository;
    }

    public function onCheckoutAddressingInitialize(GenericEvent $event)
    {
        $order = $event->getSubject();
        $this->processor->applyState($order, 'order', 'STATE_PENDING');

        $this->em->flush($order);
    }

    public function onPaymentPostStateChange(GenericEvent $event)
    {
        $payment = $event->getSubject();

        $order = $this->orderRepository->findOneByPayment($payment);

        if ($payment->getState() === PaymentInterface::STATE_COMPLETED) {
            $this->processor->applyState($order, 'order', 'STATE_NEW');
        }

        $this->em->flush($order);
    }
}
