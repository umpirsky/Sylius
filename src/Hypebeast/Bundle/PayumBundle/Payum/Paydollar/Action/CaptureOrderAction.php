<?php

namespace Hypebeast\Bundle\PayumBundle\Payum\Paydollar\Action;

use Hypebeast\Paydollar\DirectClientSideConnection\Api;
use Hypebeast\Paydollar\Model\PaymentDetails;
use Payum\Bundle\PayumBundle\Security\TokenFactory;
use Payum\Core\Action\PaymentAwareAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\SecuredCaptureRequest;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\PayumBundle\Payum\Request\ObtainCreditCardRequest;

class CaptureOrderAction extends PaymentAwareAction
{
    /**
     * @var TokenFactory
     */
    protected $tokenFactory;

    /**
     * @param TokenFactory $tokenFactory
     */
    public function __construct(TokenFactory $tokenFactory)
    {
        $this->tokenFactory = $tokenFactory;
    }

    /**
     * Pass ObtainCreditCardRequest to ObtainCreditCardAction for credit card form processing
     *
     * @param  mixed                        $request
     * @throws RequestNotSupportedException
     * @throws \Exception
     * @return void
     */
    public function execute($request)
    {
        /** @var $request SecuredCaptureRequest */
        if (!$this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }

        /** @var OrderInterface $order */
        $order = $request->getModel();
        $payment = $order->getPayment();
        $details = $payment->getDetails();

        if (empty($details)) {
            // Create payment details with form data
            $details = new PaymentDetails();
            $details->setOrderRef('SYLIUS-'.$order->getNumber().'-'.rand(1000, 9999));
            $details->setAmount(number_format($order->getTotal() / 100, 2));
            // TODO: Use $order->getCurrency()
            $details->setCurrCode(840);
            $details->setLang('E');
            $details->setSuccessUrl($request->getToken()->getTargetUrl().'?paydollar=pass');
            $details->setFailUrl($request->getToken()->getTargetUrl().'?paydollar=failed');
            $details->setErrorUrl($request->getToken()->getTargetUrl().'?paydollar=error');
            $details->setPayType(Api::PAYMENTTYPE_NORMAL);

            // Create notify token
            $notifyToken = $this->tokenFactory->createTokenForRoute(
                $request->getToken()->getPaymentName(),
                $order,
                'hypebeast_payum_paydollar_datafeed'
            );

            $details->setRemark($notifyToken->getHash());

            $details = ArrayObject::ensureArrayObject($details)->toUnsafeArray();

            $payment->setDetails($details);
            $payment->setAmount($order->getTotal());
            $payment->setCurrency($order->getCurrency());
        }

        try {
            $request->setModel($payment);

            // Go to CaptureOnsiteAction, return details array
            $this->payment->execute($request);

            /** @var $details ArrayObject */
            $details = $request->getModel();

            $payment->setDetails($details->toUnsafeArray());
        } catch (\Exception $e) {
            $request->setModel($order);

            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof SecuredCaptureRequest &&
            $request->getModel() instanceof OrderInterface
            ;
    }
}
