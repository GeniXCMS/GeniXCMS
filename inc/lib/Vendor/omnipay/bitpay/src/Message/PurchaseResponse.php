<?php

namespace Omnipay\BitPay\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * BitPay Purchase Response
 */
class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    public function isSuccessful()
    {
        return false;
    }

    public function isRedirect()
    {
        return !isset($this->data['error']);
    }

    public function getMessage()
    {
        if (isset($this->data['error'])) {
            return $this->data['error']['type'] . ': ' . $this->data['error']['message'];
        }
    }

    public function getTransactionReference()
    {
        if (isset($this->data['id'])) {
            return $this->data['id'];
        }
    }

    public function getRedirectUrl()
    {
        if (isset($this->data['url'])) {
            return $this->data['url'];
        }
    }

    public function getRedirectMethod()
    {
        return 'GET';
    }

    public function getRedirectData()
    {
    }
}
