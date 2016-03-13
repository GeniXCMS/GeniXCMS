<?php

/**
 * Stripe Update Customer Request.
 */
namespace Omnipay\Stripe\Message;

/**
 * Stripe Update Customer Request.
 *
 * Customer objects allow you to perform recurring charges and
 * track multiple charges that are associated with the same customer.
 * The API allows you to create, delete, and update your customers.
 * You can retrieve individual customers as well as a list of all of
 * your customers.
 *
 * This request updates the specified customer by setting the values
 * of the parameters passed. Any parameters not provided will be left
 * unchanged. For example, if you pass the card parameter, that becomes
 * the customer's active card to be used for all charges in the future,
 * and the customer email address is updated to the email address
 * on the card. When you update a customer to a new valid card: for
 * each of the customer's current subscriptions, if the subscription
 * is in the `past_due` state, then the latest unpaid, unclosed
 * invoice for the subscription will be retried (note that this retry
 * will not count as an automatic retry, and will not affect the next
 * regularly scheduled payment for the invoice). (Note also that no
 * invoices pertaining to subscriptions in the `unpaid` state, or
 * invoices pertaining to canceled subscriptions, will be retried as
 * a result of updating the customer's card.)
 *
 * This request accepts mostly the same arguments as the customer
 * creation call.
 *
 * @link https://stripe.com/docs/api#update_customer
 */
class UpdateCustomerRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('customerReference');
        $data = array();
        $data['description'] = $this->getDescription();

        if ($this->getToken()) {
            $data['card'] = $this->getToken();
        } elseif ($this->getCard()) {
            $this->getCard()->validate();
            $data['card'] = $this->getCardData();
            $data['email'] = $this->getCard()->getEmail();
        }

        return $data;
    }

    public function getEndpoint()
    {
        return $this->endpoint.'/customers/'.$this->getCustomerReference();
    }
}
