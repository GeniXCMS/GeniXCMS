<?php

namespace Omnipay\BitPay\Message;

use Omnipay\Tests\TestCase;

class PurchaseResponseTest extends TestCase
{
    public function testPurchaseSuccess()
    {
        $httpResponse = $this->getMockHttpResponse('PurchaseSuccess.txt');
        $response = new PurchaseResponse($this->getMockRequest(), $httpResponse->json());

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNull($response->getMessage());
        $this->assertSame('3kTrcSY8gXaSKnYGcYJCcz', $response->getTransactionReference());
        $this->assertSame('https://bitpay.com/invoice?id=3kTrcSY8gXaSKnYGcYJCcz', $response->getRedirectUrl());
        $this->assertSame('GET', $response->getRedirectMethod());
        $this->assertNull($response->getRedirectData());
    }

    public function testPurchaseFailure()
    {
        $httpResponse = $this->getMockHttpResponse('PurchaseFailure.txt');
        $response = new PurchaseResponse($this->getMockRequest(), $httpResponse->json());

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('limitExceeded: Invoice not created due to account limits, please check your approval levels', $response->getMessage());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getRedirectUrl());
        $this->assertNull($response->getRedirectData());
    }
}
