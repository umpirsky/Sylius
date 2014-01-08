<?php

namespace Hypebeast\Bundle\PayumBundle\Controller;

use Hypebeast\Bundle\PayumBundle\Security\Paydollar\HttpRequestVerifier;
use Payum\Bundle\PayumBundle\Controller\PayumController;
use Payum\Core\Request\SecuredNotifyRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PaydollarController extends PayumController
{
    public function datafeedAction(Request $request)
    {
        $token = $this->getHttpRequestVerifier()->verify($request);

        $payment = $this->getPayum()->getPayment($token->getPaymentName());

        $payment->execute(new SecuredNotifyRequest(
            $request->request->all(),
            $token
        ));

        return new Response('OK', 200);
    }

    /**
     * @return HttpRequestVerifier
     */
    protected function getHttpRequestVerifier()
    {
        return $this->get('hypebeast.security.paydollar.http_request_verifier');
    }
}
