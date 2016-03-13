<?php

namespace Omnipay\Stripe\Message;

use Omnipay\Tests\TestCase;

class FetchTransactionRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new FetchTransactionRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->setTransactionReference('ch_29yrvk84GVDsq9');
    }

    public function testEndpoint()
    {
        $this->assertSame('https://api.stripe.com/v1/charges/ch_29yrvk84GVDsq9', $this->request->getEndpoint());
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('FetchTransactionSuccess.txt');
        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('ch_29yrvk84GVDsq9', $response->getTransactionReference());
        $this->assertNull($response->getCardReference());
        $this->assertNull($response->getMessage());
    }

    public function testSendError()
    {
        $this->setMockHttpResponse('FetchTransactionFailure.txt');
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getCardReference());
        $this->assertSame('No such charge: ch_29yrvk84GVDsq9fake', $response->getMessage());
    }
}
