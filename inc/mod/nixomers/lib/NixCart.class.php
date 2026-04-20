<?php
/**
 * NixCart Class
 * Handles Shopping Cart rendering and related products
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

class NixCart
{
    /**
     * Main Cart Page Renderer
     */
    public static function render()
    {
        $cart = $_SESSION['nix_cart'] ?? [];
        $currency = Options::v('nixomers_currency') ?: 'IDR';
        $framework = Nixomers::getFramework();
        $total = 0;
        $items = [];

        if (!empty($cart)) {
            foreach ($cart as $id => $qty) {
                $p = Posts::fetch($id)[0] ?? null;
                if ($p) {
                    $price = Posts::getParam('price', $id) ?: 0;
                    $items[] = [
                        'id' => $id,
                        'qty' => $qty,
                        'product' => $p,
                        'price' => $price,
                        'subtotal' => $price * $qty
                    ];
                    $total += $price * $qty;
                }
            }
        }

        $taxRate = (float) Options::v('nixomers_tax') ?: 0;
        $taxAmount = $total * ($taxRate / 100);
        $grandTotal = $total + $taxAmount;

        $excludeIds = array_keys($cart);
        $relatedQuery = Query::table('posts')->where('type', 'nixomers')->where('status', '1');
        if (!empty($excludeIds)) {
            $phs = implode(',', array_fill(0, count($excludeIds), '?'));
            $relatedQuery->whereRaw("id NOT IN ($phs)", $excludeIds);
        }
        
        $randomFunc = (defined('DB_DRIVER') && DB_DRIVER === 'sqlite') ? 'RANDOM()' : 'RAND()';
        $related = $relatedQuery->limit(2)->orderByRaw($randomFunc)->get();

        // Theme Override
        $themeOut = Nixomers::renderThemeView('cart', [
            'items' => $items,
            'total' => $total,
            'taxRate' => $taxRate,
            'taxAmount' => $taxAmount,
            'grandTotal' => $grandTotal,
            'related' => $related
        ]);
        if ($themeOut !== false) return $themeOut;

        if ($framework === 'tailwindcss') {
            $html = '<div class="overflow-x-auto bg-white rounded-3xl shadow-sm p-6 border border-gray-100">';
            $html .= '<table class="w-full text-left border-collapse">';
            $html .= '<thead class="text-gray-400 text-xs uppercase font-bold border-b border-gray-50"><tr><th class="py-4">Product</th><th class="text-center py-4">Qty</th><th class="text-right py-4">Price</th><th class="text-right py-4">Total</th><th class="text-center py-4">#</th></tr></thead><tbody class="divide-y divide-gray-50">';

            if (!empty($cart)) {
                foreach ($items as $item) {
                    $html .= '
                    <tr>
                        <td class="py-6">
                            <div class="flex items-center gap-4">
                                <img src="' . (Posts::getPostImage($item['id']) ?: Site::$url . '/assets/images/noimage.png') . '" class="w-16 h-16 rounded-2xl shadow-sm object-cover">
                                <div>
                                    <h6 class="font-bold text-gray-900">' . $item['product']->title . '</h6>
                                    <small class="text-gray-400">SKU: ' . Posts::getParam('sku', $item['id']) . '</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center py-6">
                            <form method="post" class="flex items-center justify-center gap-2">
                                <input type="hidden" name="product_id" value="' . $item['id'] . '">
                                <input type="hidden" name="nix_action" value="update">
                                <input type="number" name="qty" value="' . $item['qty'] . '" class="w-16 p-2 text-center border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-100 outline-none transition-all" onchange="this.form.submit()">
                            </form>
                        </td>
                        <td class="text-right py-6 text-gray-600">' . $currency . ' ' . Nixomers::formatCurrency($item['price']) . '</td>
                        <td class="text-right py-6 font-bold text-gray-900">' . $currency . ' ' . Nixomers::formatCurrency($item['subtotal']) . '</td>
                        <td class="text-center py-6">
                            <form method="post">
                                <input type="hidden" name="product_id" value="' . $item['id'] . '">
                                <input type="hidden" name="nix_action" value="remove">
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-full transition-colors"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>';
                }

                $html .= '</tbody><tfoot class="border-t-2 border-gray-50">';
                $html .= '<tr><td colspan="3" class="text-right py-3 text-gray-500">Subtotal:</td><td class="text-right py-3 font-bold text-gray-900">' . $currency . ' ' . Nixomers::formatCurrency($total) . '</td><td></td></tr>';
                $html .= '<tr><td colspan="3" class="text-right py-3 text-gray-500">Tax (' . $taxRate . '%):</td><td class="text-right py-3 font-bold text-gray-900">' . $currency . ' ' . Nixomers::formatCurrency($taxAmount) . '</td><td></td></tr>';
                $html .= '<tr><td colspan="3" class="text-right py-6 font-bold text-xl text-gray-900">Grand Total:</td><td class="text-right py-6 font-extrabold text-2xl text-blue-600">' . $currency . ' ' . Nixomers::formatCurrency($grandTotal) . '</td><td></td></tr>';
                $html .= '</tfoot></table></div>';
                $html .= '<div class="flex flex-col md:flex-row justify-between items-center gap-4 mt-8">
                            <a href="' . Url::mod('store') . '" class="w-full md:w-auto text-center px-8 py-4 bg-gray-50 text-gray-600 font-bold rounded-full hover:bg-gray-100 transition-colors"><i class="bi bi-arrow-left mr-2"></i> Continue Shopping</a>
                            <a href="' . Url::mod('checkout') . '" class="w-full md:w-auto text-center px-10 py-4 bg-blue-600 text-white font-bold rounded-full shadow-lg shadow-blue-100 hover:bg-blue-700 hover:-translate-y-1 transition-all">Proceed to Checkout <i class="bi bi-credit-card-2-front ml-2"></i></a>
                          </div>';
            } else {
                $html .= '<tr><td colspan="5" class="text-center py-20 text-gray-400">Your cart is empty. <a href="' . Url::mod('store') . '" class="text-blue-600 font-bold hover:underline">Go shopping!</a></td></tr></tbody></table></div>';
            }
        } else {
            $html = '<div class="table-responsive bg-white rounded-4 shadow-sm p-4 w-100 mb-4">';
            $html .= '<table class="table table-hover align-middle mb-0 w-100" style="min-width: 600px;">';
            $html .= '<thead class="text-muted small text-uppercase fw-bold border-bottom">
                        <tr>
                            <th style="width: 50%;">Product</th>
                            <th class="text-center" style="width: 15%;">Qty</th>
                            <th class="text-end" style="width: 15%;">Price</th>
                            <th class="text-end" style="width: 15%;">Total</th>
                            <th class="text-center" style="width: 5%;">#</th>
                        </tr>
                      </thead>
                      <tbody>';

            if (!empty($cart)) {
                foreach ($items as $item) {
                    $html .= '
                    <tr class="border-bottom-custom">
                        <td class="py-4">
                            <div class="d-flex align-items-center gap-3">
                                <img src="' . (Posts::getPostImage($item['id']) ?: Site::$url . '/assets/images/noimage.png') . '" width="64" height="64" class="rounded-3 shadow-sm object-fit-cover">
                                <div>
                                    <h6 class="mb-1 fw-bold">' . $item['product']->title . '</h6>
                                    <div class="text-muted" style="font-size: 11px; letter-spacing: 0.05em; font-weight: 700;">SKU: ' . Posts::getParam('sku', $item['id']) . '</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <form method="post">
                                <input type="hidden" name="product_id" value="' . $item['id'] . '">
                                <input type="hidden" name="nix_action" value="update">
                                <input type="number" name="qty" value="' . $item['qty'] . '" class="form-control form-control-sm text-center mx-auto" style="width:70px; height: 40px; border-radius: 10px;" onchange="this.form.submit()">
                            </form>
                        </td>
                        <td class="text-end">' . $currency . ' ' . Nixomers::formatCurrency($item['price']) . '</td>
                        <td class="text-end fw-bold">' . $currency . ' ' . Nixomers::formatCurrency($item['subtotal']) . '</td>
                        <td class="text-center">
                            <form method="post">
                                <input type="hidden" name="product_id" value="' . $item['id'] . '">
                                <input type="hidden" name="nix_action" value="remove">
                                <button type="submit" class="btn btn-sm btn-light rounded-circle text-muted hover-danger" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-x-lg" style="font-size: 12px;"></i></button>
                            </form>
                        </td>
                    </tr>';
                }

                $html .= '</tbody><tfoot class="border-top">';
                $html .= '<tr><td colspan="3" class="text-end py-3 border-0 text-muted">Subtotal:</td><td class="text-end py-3 border-0 fw-bold">' . $currency . ' ' . Nixomers::formatCurrency($total) . '</td><td></td></tr>';
                $html .= '<tr><td colspan="3" class="text-end py-2 border-0 text-muted">Tax (' . $taxRate . '%):</td><td class="text-end py-2 border-0 fw-bold">' . $currency . ' ' . Nixomers::formatCurrency($taxAmount) . '</td><td></td></tr>';
                $html .= '<tr><td colspan="3" class="text-end py-4 fw-black fs-4 border-0">Grand Total:</td><td class="text-end py-4 fw-black fs-4 text-primary border-0">' . $currency . ' ' . Nixomers::formatCurrency($grandTotal) . '</td><td></td></tr>';
                $html .= '</tfoot></table></div>';
                $html .= '<div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mt-4">
                            <a href="' . Url::mod('store') . '" class="btn btn-light rounded-pill px-4 py-3 fw-bold order-2 order-md-1"><i class="bi bi-arrow-left me-2"></i> Continue Shopping</a>
                            <a href="' . Url::mod('checkout') . '" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-sm order-1 order-md-2">Proceed to Checkout <i class="bi bi-credit-card-2-front ms-2"></i></a>
                          </div>';
            } else {
                $html .= '<tr><td colspan="5" class="text-center py-5 text-muted">Your cart is empty. <a href="' . Url::mod('store') . '" class="fw-bold">Go shopping!</a></td></tr></tbody></table></div>';
            }
        }

        return $html;
    }

    /**
     * Get Total Items in Cart
     */
    public static function count()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $cart = $_SESSION['nix_cart'] ?? [];
        return array_sum($cart);
    }

    /**
     * Get HTML Badge for Cart
     */
    public static function getBadge($args = '')
    {
        $class = is_array($args) ? ($args[0] ?? '') : $args;
        $count = self::count();
        $display = ($count > 0) ? 'inline-block' : 'none';
        return '<span class="nix-cart-count ' . $class . '" style="display: ' . $display . ';">' . $count . '</span>';
    }
}
