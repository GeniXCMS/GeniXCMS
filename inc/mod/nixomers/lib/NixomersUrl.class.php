<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * NixomersUrl Class
 * 
 * Centralized utility for Nixomers URL generation to ensure consistency
 * across Smart URL and Standard URL modes.
 */
class NixomersUrl
{
    /**
     * Generates a URL to the order detail page.
     * 
     * @param string|int $orderId The Order ID or DB ID
     * @return string
     */
    public static function orderDetail($orderId)
    {
        if (SMART_URL) {
            $inFold = (Options::v('permalink_use_index_php') == 'on') ? 'index.php/' : '';
            return Site::$url . $inFold . 'order/detail/' . $orderId . GX_URL_PREFIX;
        } else {
            $url = Url::mod('purchase_detail');
            $url .= (strpos($url, '?') !== false ? '&' : '?') . 'order_id=' . $orderId;
            return $url;
        }
    }

    /**
     * Generates a URL to the payment confirmation page.
     * 
     * @param string|int $orderId
     * @return string
     */
    public static function payment($orderId)
    {
        $url = Url::mod('payment');
        $url .= (strpos($url, '?') !== false ? '&' : '?') . 'order_id=' . $orderId;
        return $url;
    }

    /**
     * Generates a URL to the main store catalog.
     * 
     * @return string
     */
    public static function store()
    {
        return Url::mod('store');
    }

    /**
     * Generates a URL to the shopping cart.
     * 
     * @return string
     */
    public static function cart()
    {
        return Url::mod('cart');
    }

    /**
     * Generates a URL to the checkout page.
     * 
     * @return string
     */
    public static function checkout()
    {
        return Url::mod('checkout');
    }

    /**
     * Generates a URL to a specific product.
     * 
     * @param int|string $id Product ID or Object
     * @return string
     */
    public static function product($id)
    {
        if (is_object($id)) {
            $id = $id->id;
        }

        if (SMART_URL) {
            $inFold = (Options::v('permalink_use_index_php') == 'on') ? 'index.php/' : '';
            return Site::$url . $inFold . 'product/' . Url::slug($id) . GX_URL_PREFIX;
        } else {
            return Site::$url . '?post=' . $id;
        }
    }
}
