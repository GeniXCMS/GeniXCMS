<?php

namespace Omnipay\Stripe\Message;

use Omnipay\Tests\TestCase;

class RefundRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->setTransactionReference('ch_12RgN9L7XhO9mI')
            ->setAmount('10.00')->setRefundApplicationFee(true);
    }

    public function testEndpoint()
    {
        $this->assertSame('https://api.stripe.com/v1/charges/ch_12RgN9L7XhO9mI/refund', $this->request->getEndpoint());
    }

    public function testAmount()
    {
        $data = $this->request->getData();
        $this->assertSame(1000, $data['amount']);
    }

    public function testRefundApplicationFee()
    {
        $data = $this->request->getData();
        $this->assertEquals("true", $data['refund_application_fee']);
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('RefundSuccess.txt');
        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('ch_12RgN9L7XhO9mI', $response->getTransactionReference());
        $this->assertNull($response->getCardReference());
        $this->assertNull($response->getMessage());
    }

    public function testSendError()
    {
        $this->setMockHttpResponse('RefundFailure.txt');
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getCardReference());
        $this->assertSame('Charge ch_12RgN9L7XhO9mI has already been refunded.', $response->getMessage());
    }
}
