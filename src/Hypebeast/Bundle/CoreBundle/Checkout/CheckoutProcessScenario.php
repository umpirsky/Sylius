<?php

namespace Hypebeast\Bundle\CoreBundle\Checkout;

use Sylius\Bundle\CoreBundle\Checkout\CheckoutProcessScenario as BaseCheckoutProcessScenario;
use Sylius\Bundle\FlowBundle\Process\Builder\ProcessBuilderInterface;

class CheckoutProcessScenario extends BaseCheckoutProcessScenario
{
    public function build(ProcessBuilderInterface $builder)
    {
        $cart = $this->getCurrentCart();

        $builder
            ->add('security', 'sylius_checkout_security')
            ->add('addressing', 'sylius_checkout_addressing')
            ->add('delivery_and_payment', 'hypebeast_checkout_delivery_and_payment')
            ->add('finalize', 'sylius_checkout_finalize')
            ->add('purchase', 'sylius_checkout_purchase')
        ;

        $builder
            ->setDisplayRoute('sylius_checkout_display')
            ->setForwardRoute('sylius_checkout_forward')
            ->setRedirect('sylius_homepage')
            ->validate(function () use ($cart) {
                return !$cart->isEmpty();
            })
        ;
    }
}
