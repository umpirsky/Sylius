<?php

namespace Hypebeast\Bundle\WebBundle\Checkout\Step;

use Sylius\Bundle\CoreBundle\Checkout\Step\ShippingStep as BaseStep;
use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Symfony\Component\Form\FormInterface;

class ShippingStep extends BaseStep
{
    protected function renderStep(ProcessContextInterface $context, OrderInterface $order, FormInterface $form)
    {
        return $this->render('HypebeastWebBundle:Frontend/Checkout/Step:shipping.html.twig', array(
            'order'   => $order,
            'form'    => $form->createView(),
            'context' => $context
        ));
    }
}
