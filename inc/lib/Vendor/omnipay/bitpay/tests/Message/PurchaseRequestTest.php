<?php

namespace Omnipay\BitPay\Message;

use Omnipay\Tests\TestCase;

class PurchaseRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'amount' => '12.00',
                'currency' => 'AUD',
                'transactionId' => '5',
                'description' => 'thing',
                'notifyUrl' => 'https://www.example.com/notify',
                'returnUrl' => 'https://www.example.com/return',
            )
        );
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame('12.00', $data['price']);
        $this->assertSame('AUD', $data['currency']);
        $this->assertSame('5', $data['posData']);
        $this->assertSame('thing', $data['itemDesc']);
        $this->assertSame('https://www.example.com/notify', $data['notificationURL']);
        $this->assertSame('https://www.example.com/return', $data['redirectURL']);
    }

    public function testSend()
    {
        $this->setMockHttpResponse('PurchaseSuccess.txt');
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
    }

    public function testGetEndpoint()
    {
        $this->request->setTestMode(false);
        $this->assertSame('https://bitpay.com/api', $this->request->getEndpoint());
        $this->request->setTestMode(true);
        $this->assertSame('https://test.bitpay.com/api', $this->request->getEndpoint());
    }
}
