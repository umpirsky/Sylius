<?php

namespace Hypebeast\Bundle\CoreBundle\EventListener;

use Symfony\Component\EventDispatcher\GenericEvent;
use Sylius\Bundle\CartBundle\Event\CartEvent;
use Sylius\Bundle\CartBundle\Event\CartItemEvent;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\PaymentsBundle\Model\PaymentInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OrderGiftCardListener
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onCartItemAddInitialize(CartItemEvent $event)
    {
        $request = $this->container->get('request');
        $variant = $event->getItem()->getVariant();
        $form = $this->container->get('form.factory')->create('sylius_gift_card');

        if ($variant->getProduct()->isGiftCard() && $request->isMethod('POST') && $form->bind($request)->isValid()) {
            $giftCard = $form->getData();
            $giftCard->setVariant($variant);
            $giftCard->setValue($variant->getPrice());

            $cart = $event->getCart();
            $cart->addGiftCard($giftCard);
        }
    }

    public function onCartItemRemoveInitialize(CartItemEvent $event)
    {
        $variant = $event->getItem()->getVariant();
        $cart = $event->getCart();

        foreach ($cart->getGiftCards() as $giftCard) {
            if ($giftCard->getVariant() === $variant) {
               $cart->removeGiftCard($giftCard);
            }
        }
    }

    public function onCartClearInitialize(CartEvent $event)
    {
        $event->getCart()->removeGiftCards();
    }

    public function onCheckoutFinalizePreComplete(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new \InvalidArgumentException(
                'Event subject to be instance of "Sylius\Bundle\CoreBundle\Model\OrderInterface".'
            );
        }

        foreach ($order->getGiftCards() as $giftCard) {
            $giftCard->setStatus($giftCard::STATUS_ORDERED);
        }
    }

    public function onPaymentPostStateChange(GenericEvent $event)
    {
        $payment = $event->getSubject();

        if (!$payment instanceof PaymentInterface) {
            throw new \InvalidArgumentException(
                'Event subject to be instance of "Sylius\Bundle\PaymentsBundle\Model\PaymentInterface".'
            );
        }

        $order = $this->container->get('sylius.repository.order')->findOneBy(['payment' => $payment]);
        if (null === $order) {
            throw new \InvalidArgumentException(
                'Order not found.'
            );
        }

        if ($payment::STATE_COMPLETED === $payment->getState()) {
            foreach ($order->getGiftCards() as $giftCard) {
                $this->container->get('sylius.processor.gift_card')->send($giftCard);
            }
        }
    }
}
