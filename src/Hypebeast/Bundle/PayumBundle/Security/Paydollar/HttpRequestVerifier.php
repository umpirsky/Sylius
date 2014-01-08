<?php

namespace Hypebeast\Bundle\PayumBundle\Security\Paydollar;

use Payum\Bundle\PayumBundle\Security\HttpRequestVerifier as BaseHttpRequestVerifier;
use Payum\Core\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HttpRequestVerifier extends BaseHttpRequestVerifier
{
    /**
     * {@inheritDoc}
     */
    public function verify($request)
    {
        if (false == $request instanceof Request) {
            throw new InvalidArgumentException(sprintf(
                'Invalid request given. Expected %s but it is %s',
                'Symfony\Component\HttpFoundation\Request',
                is_object($request) ? get_class($request) : gettype($request)
            ));
        }

        if (false === $hash = $request->request->get('remark', false)) {
            throw new NotFoundHttpException('Remark parameter is not set in datafeed.');
        }

        if (false == $token = $this->tokenStorage->findModelById($hash)) {
            throw new NotFoundHttpException(sprintf('A token with hash `%s` could not be found.', $hash));
        }

        return $token;
    }
}
