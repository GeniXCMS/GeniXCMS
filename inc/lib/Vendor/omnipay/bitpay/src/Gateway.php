<?php

namespace Omnipay\BitPay;

use Omnipay\Common\AbstractGateway;

/**
 * BitPay Gateway
 *
 * @link https://bitpay.com/downloads/bitpayApi.pdf
 */
class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'BitPay';
    }

    public function getDefaultParameters()
    {
        return array(
            'apiKey' => '',
            'testMode' => false,
        );
    }

    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }

    public function setApiKey($value)
    {
        return $this->setParameter('apiKey', $value);
    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\BitPay\Message\PurchaseRequest', $parameters);
    }
}
