<?php

namespace Hypebeast\Bundle\CoreBundle\EventListener;

use Symfony\Component\EventDispatcher\GenericEvent;
use Sylius\Bundle\PaymentsBundle\Model\PaymentInterface;
use Hypebeast\Bundle\CoreBundle\Builder\RewardBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Hypebeast\Bundle\CoreBundle\Entity\Reward;

class RewardListener
{
    private $securityContext;
    private $rewardBuilder;
    private $orderRepository;

    public function __construct(SecurityContextInterface $securityContext, RewardBuilder $rewardBuilder, EntityRepository $orderRepository)
    {
        $this->securityContext = $securityContext;
        $this->rewardBuilder = $rewardBuilder;
        $this->orderRepository = $orderRepository;
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

        $order = $this->orderRepository->findOneBy(['payment' => $payment]);
        if (null === $order) {
            throw new \InvalidArgumentException(
                'Order not found.'
            );
        }

        $this->rewardBuilder
            ->create(
                Reward::TYPE_ORDER,
                floor(($order->getTotal() - $order->getOnSaleTotal() - $order->getShippingTotal()) / 100),
                $order->getId()
            )
            ->save()
        ;
    }

    private function getUser()
    {
        if ($this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->securityContext->getToken()->getUser();
        }
    }
}
