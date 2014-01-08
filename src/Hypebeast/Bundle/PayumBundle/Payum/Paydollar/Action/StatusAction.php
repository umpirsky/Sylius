<?php

namespace Hypebeast\Bundle\PayumBundle\Payum\Paydollar\Action;

use Hypebeast\Paydollar\Api;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\StatusRequestInterface;

class StatusAction implements ActionInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute($request)
    {
        /** @var $request StatusRequestInterface */
        if (false == $this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }

        $model = new ArrayObject($request->getModel());

        if (isset($model['successcode'])) {
            // Datafeed
            if ($model['successcode'] === Api::SUCCESSCODE_SUCCEEDED) {
                $request->markSuccess();

                return;
            }

            $request->markFailed();

            return;
        } elseif (isset($model['paydollar'])) {
            // Return from PayDollar connection page.
            if ($model['paydollar'] == 'pass') {
                $request->markPending();

                return;
            }

            $request->markFailed();

            return;
        }

        $request->markNew();
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        if (false == $request instanceof StatusRequestInterface) {
            return false;
        }

        $model = $request->getModel();
        if (false == $model instanceof \ArrayAccess) {
            return false;
        }

        return isset($model['orderRef']) || isset($model['Ref']);
    }
}
