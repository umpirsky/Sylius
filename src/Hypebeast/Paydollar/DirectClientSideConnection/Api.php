<?php

namespace Hypebeast\Paydollar\DirectClientSideConnection;
use Buzz\Message\Response;
use Payum\Core\Exception\InvalidArgumentException;

class Api extends \Hypebeast\Paydollar\Api
{
    /**
     * @var array
     */
    protected $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function prepareOnsitePayment(array $params)
    {
        $supportedParams = [
            'orderRef' => null,
            'amount' => null,
            'currCode' => null,
            'lang' => null,
            'pMethod' => null,
            'epMonth' => null,
            'epYear' => null,
            'cardNo' => null,
            'securityCode' => null,
            'cardHolder' => null,
            'failUrl' => null,
            'successUrl' => null,
            'errorUrl' => null,
            'payType' => null,
            'remark' => null,
        ];

        $params = array_filter(array_replace(
            $supportedParams,
            array_intersect_key($params, $supportedParams)
        ));

        $params['merchantId'] = $this->options['merchant_id'];

        if (in_array($this->options['payment_method'], ['VISA', 'Master', 'AMEX'])) {
            $params['pMethod'] = $this->options['payment_method'];
        } elseif (in_array($this->options['payment_method'], ['ALIPAY', 'UPOP'])) {
            $params['payMethod'] = $this->options['payment_method'];
        }

        return $params;
    }

    /**
     * @return string
     */
    public function getApiEndpoint()
    {
        if (in_array($this->options['payment_method'], ['VISA', 'Master', 'AMEX'])) {
            if ($this->options['sandbox']) {
                return "https://test.paydollar.com/b2cDemo/eng/dPayment/payComp.jsp";
            }

            return "https://www.paydollar.com/b2c2/eng/dPayment/payComp.jsp";
        }

        if (in_array($this->options['payment_method'], ['ALIPAY', 'UPOP'])) {
            if ($this->options['sandbox']) {
                return "https://test.paydollar.com/b2cDemo/eng/payment/payForm.jsp";
            }

            return "https://www.paydollar.com/b2c2/eng/payment/payForm.jsp";
        }

        throw new InvalidArgumentException('Unknown PayDollar payment method.');
    }

    /**
     * @return Response
     */
    protected function createResponse()
    {
        return new Response();
    }
}
