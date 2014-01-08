<?php

namespace Hypebeast\Bundle\PayumBundle\Payum\Paydollar\Action;

use Hypebeast\Paydollar\DirectClientSideConnection\Api;
use Payum\Core\Action\PaymentAwareAction;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\Request\CaptureRequest;
use Payum\Core\Request\GetHttpQueryRequest;
use Payum\Core\Request\PostRedirectUrlInteractiveRequest;
use Sylius\Bundle\PaymentsBundle\Model\PaymentInterface;

class CaptureOnsiteAction extends PaymentAwareAction implements ApiAwareInterface
{
    /**
     * @var Api
     */
    protected $api;

    public function setApi($api)
    {
        if (false === $api instanceof Api) {
            throw new UnsupportedApiException('Not supported.');
        }

        $this->api = $api;
    }

    public function execute($request)
    {
        /** @var $request CaptureRequest */
        if (false == $this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }

        $details = ArrayObject::ensureArrayObject($request->getModel()->getDetails());

        // Load HTTP GET parameters.
        $this->payment->execute($getHttpQuery = new GetHttpQueryRequest());

        if (false === isset($getHttpQuery['paydollar'])) {
            // Redirection page
            throw new PostRedirectUrlInteractiveRequest(
                $this->api->getApiEndpoint(),
                $this->api->prepareOnsitePayment($details->toUnsafeArray())
            );
        } else {
            // Back from payment gateway
            $details->replace($getHttpQuery);
            $request->setModel($details);
        }
    }

    public function supports($request)
    {
        if (false == $request instanceof CaptureRequest) {
            return false;
        }

        if (false == $request->getModel() instanceof PaymentInterface) {
            return false;
        }

        return $this->isPaymentValid($request->getModel());
    }

    private function isPaymentValid(PaymentInterface $payment)
    {
        $details = $payment->getDetails();

        if (!isset($details['orderRef']) || empty($details['orderRef'])) {
            return false;
        }

        if (!isset($details['amount']) || empty($details['amount'])) {
            return false;
        }

        return true;
    }
}
