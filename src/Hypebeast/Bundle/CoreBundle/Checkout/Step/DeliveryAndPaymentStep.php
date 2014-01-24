<?php

namespace Hypebeast\Bundle\CoreBundle\Checkout\Step;

use Sylius\Bundle\CoreBundle\Checkout\Step\CheckoutStep;
use Sylius\Bundle\CoreBundle\Model\Order;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Sylius\Bundle\CoreBundle\Checkout\SyliusCheckoutEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

class DeliveryAndPaymentStep extends CheckoutStep
{
    /**
     * Display action.
     *
     * @param ProcessContextInterface $context
     * @return Response
     */
    public function displayAction(ProcessContextInterface $context)
    {
        $order = $this->getCurrentCart();
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SHIPPING_INITIALIZE, $order);
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::PAYMENT_INITIALIZE, $order);
        $form = $this->createDeliveryAndPaymentForm($order);

        return $this->renderStep($context, $order, $form);
    }

    public function forwardAction(ProcessContextInterface $context)
    {
        $request = $this->getRequest();

        /** @var $order Order */
        $order = $this->getCurrentCart();

        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SHIPPING_INITIALIZE, $order);
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::PAYMENT_INITIALIZE, $order);

        $form = $this->createDeliveryAndPaymentForm($order);

        if ($request->isMethod('POST') && $form->bind($request)->isValid()) {
            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SHIPPING_PRE_COMPLETE, $order);
            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::PAYMENT_PRE_COMPLETE, $order);

            $this->getManager()->persist($order);
            $this->getManager()->flush();

            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SHIPPING_COMPLETE, $order);
            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::PAYMENT_COMPLETE, $order);

            return $this->complete();
        }

        return $this->renderStep($context, $order, $form);
    }

    /**
     * @param ProcessContextInterface $context
     * @param OrderInterface          $order
     * @param FormInterface           $form
     * @return Response
     */
    protected function renderStep(ProcessContextInterface $context, OrderInterface $order, FormInterface $form)
    {
        return $this->render('HypebeastWebBundle:Frontend/Checkout/Step:deliveryAndPayment.html.twig', array(
            'order'   => $order,
            'form'    => $form->createView(),
            'context' => $context
        ));
    }

    /**
     * @param OrderInterface $order
     * @return FormInterface
     */
    protected function createDeliveryAndPaymentForm(OrderInterface $order)
    {
        $zone = $this->getZoneMatcher()->match($order->getShippingAddress());

        return $this->createForm('hypebeast_checkout_delivery_and_payment', $order, array(
            'criteria'  => array('zone' => $zone)
        ));
    }
}