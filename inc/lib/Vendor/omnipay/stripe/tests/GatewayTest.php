<?php

namespace Omnipay\Stripe;

use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testAuthorize()
    {
        $request = $this->gateway->authorize(array('amount' => '10.00'));

        $this->assertInstanceOf('Omnipay\Stripe\Message\AuthorizeRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testCapture()
    {
        $request = $this->gateway->capture(array('amount' => '10.00'));

        $this->assertInstanceOf('Omnipay\Stripe\Message\CaptureRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testPurchase()
    {
        $request = $this->gateway->purchase(array('amount' => '10.00'));

        $this->assertInstanceOf('Omnipay\Stripe\Message\PurchaseRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testRefund()
    {
        $request = $this->gateway->refund(array('amount' => '10.00'));

        $this->assertInstanceOf('Omnipay\Stripe\Message\RefundRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testVoid()
    {
        $request = $this->gateway->void();

        $this->assertInstanceOf('Omnipay\Stripe\Message\VoidRequest', $request);
    }

    public function testFetchTransaction()
    {
        $request = $this->gateway->fetchTransaction(array());

        $this->assertInstanceOf('Omnipay\Stripe\Message\FetchTransactionRequest', $request);
    }

    public function testFetchToken()
    {
        $request = $this->gateway->fetchToken(array());

        $this->assertInstanceOf('Omnipay\Stripe\Message\FetchTokenRequest', $request);
    }

    public function testCreateCard()
    {
        $request = $this->gateway->createCard(array('description' => 'foo'));

        $this->assertInstanceOf('Omnipay\Stripe\Message\CreateCardRequest', $request);
        $this->assertSame('foo', $request->getDescription());
    }

    public function testUpdateCard()
    {
        $request = $this->gateway->updateCard(array('cardReference' => 'cus_1MZSEtqSghKx99'));

        $this->assertInstanceOf('Omnipay\Stripe\Message\UpdateCardRequest', $request);
        $this->assertSame('cus_1MZSEtqSghKx99', $request->getCardReference());
    }

    public function testDeleteCard()
    {
        $request = $this->gateway->deleteCard(array('cardReference' => 'cus_1MZSEtqSghKx99'));

        $this->assertInstanceOf('Omnipay\Stripe\Message\DeleteCardRequest', $request);
        $this->assertSame('cus_1MZSEtqSghKx99', $request->getCardReference());
    }

    public function testCreateCustomer()
    {
        $request = $this->gateway->createCustomer(array('description' => 'foo@foo.com'));

        $this->assertInstanceOf('Omnipay\Stripe\Message\CreateCustomerRequest', $request);
        $this->assertSame('foo@foo.com', $request->getDescription());
    }

    public function testUpdateCustomer()
    {
        $request = $this->gateway->updateCustomer(array('customerReference' => 'cus_1MZSEtqSghKx99'));

        $this->assertInstanceOf('Omnipay\Stripe\Message\UpdateCustomerRequest', $request);
        $this->assertSame('cus_1MZSEtqSghKx99', $request->getCustomerReference());
    }

    public function testDeleteCustomer()
    {
        $request = $this->gateway->deleteCustomer(array('customerReference' => 'cus_1MZSEtqSghKx99'));

        $this->assertInstanceOf('Omnipay\Stripe\Message\DeleteCustomerRequest', $request);
        $this->assertSame('cus_1MZSEtqSghKx99', $request->getCustomerReference());
    }
}
