<?php

namespace Omnipay\BitPay\Message;

use Omnipay\Common\Message\AbstractRequest;

/**
 * BitPay Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{
    protected $liveEndpoint = 'https://bitpay.com/api';
    protected $testEndpoint = 'https://test.bitpay.com/api';

    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }

    public function setApiKey($value)
    {
        return $this->setParameter('apiKey', $value);
    }

    public function getData()
    {
        $this->validate('amount', 'currency');

        $data = array();
        $data['price'] = $this->getAmount();
        $data['currency'] = $this->getCurrency();
        $data['posData'] = $this->getTransactionId();
        $data['itemDesc'] = $this->getDescription();
        $data['notificationURL'] = $this->getNotifyUrl();
        $data['redirectURL'] = $this->getReturnUrl();

        return $data;
    }

    public function sendData($data)
    {
        $httpRequest = $this->httpClient->post($this->getEndpoint().'/invoice', null, $data);
        $httpResponse = $httpRequest
            ->setHeader('Authorization', 'Basic '.base64_encode($this->getApiKey().':'))
            ->send();

        return $this->response = new PurchaseResponse($this, $httpResponse->json());
    }

    public function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }
}
