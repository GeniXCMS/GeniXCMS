<?php
/**
 * NixCheckout Class
 * Handles Checkout Form rendering and Summary displays
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

class NixCheckout
{
    /**
     * Renders Step 1: Checkout Form
     */
    public static function render()
    {
        $framework = Nixomers::getFramework();

        // Calculate Totals once
        $cart = $_SESSION['nix_cart'] ?? [];
        $total = 0;
        $items = [];
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
        $taxRate = (float) Options::v('nixomers_tax') ?: 0;
        $taxAmount = $total * ($taxRate / 100);
        $grandTotal = $total + $taxAmount;

        // Theme Override
        $themeOut = Nixomers::renderThemeView('checkout', [
            'total' => $total,
            'taxAmount' => $taxAmount,
            'items' => $items,
            'grandTotal' => $grandTotal
        ]);
        if ($themeOut !== false)
            return $themeOut;

        $html = '<div class="row g-4">';
        if ($framework === 'tailwindcss') {
            $html = '<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">';
            $html .= '<div class="lg:col-span-2">' . self::showForm($framework, $total, $taxAmount) . '</div>';
            $html .= '<div class="lg:col-span-1">' . self::showSummary($framework, $total, $taxAmount, $items) . '</div>';
            $html .= '</div>';
        } else {
            $html .= '<div class="col-lg-8">' . self::showForm($framework, $total, $taxAmount) . '</div>';
            $html .= '<div class="col-lg-4">' . self::showSummary($framework, $total, $taxAmount, $items) . '</div>';
            $html .= '</div>';
        }

        return $html;
    }

    public static function showForm($framework = null, $total = 0, $taxAmount = 0)
    {
        if (!$framework)
            $framework = Nixomers::getFramework();
        $usr = Nixomers::getUser();

        $js = self::injectScripts($total, $taxAmount);

        if ($framework === 'tailwindcss') {
            $html = '
            <form method="post" action="' . Url::mod('payment') . '" class="space-y-8 nixomers-checkout-form">
                <!-- Contact Information -->
                <div class="space-y-4">
                    <div class="flex justify-between items-end border-b border-gray-200 pb-2">
                        <h2 class="text-xl font-bold text-gray-900 mb-0">Contact Information</h2>
                        ' . (!$usr ? '
                            <span class="text-sm text-gray-500">Already have an account? <a class="text-primary underline" href="' . Url::login('backto=' . urlencode(Url::mod('checkout'))) . '">Log in</a></span>
                        ' : '
                            <span class="text-xs text-green-600 bg-green-50 px-3 py-1 rounded-full"><i class="bi bi-person-check-fill me-1"></i> Logged in as ' . ($usr->fname ?: Session::val('username')) . '</span>
                        ') . '
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-500 mb-1">Email Address</label>
                        <input class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 outline-none" name="email" type="email" placeholder="Email" value="' . ($usr ? $usr->email : '') . '" required ' . ($usr ? 'readonly' : '') . '>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-500 mb-1">Phone Number</label>
                        <input class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 outline-none" name="phone" type="text" placeholder="Phone" value="' . (isset($usr->phone) ? $usr->phone : '') . '" required>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="space-y-4">
                    <h2 class="text-xl font-bold text-gray-900 border-b border-gray-200 pb-2">Shipping Address</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-500 mb-1">First name</label>
                            <input name="fname" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 outline-none" type="text" value="' . ($usr ? $usr->fname : '') . '" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-500 mb-1">Last name</label>
                            <input name="lname" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 outline-none" type="text" value="' . ($usr ? $usr->lname : '') . '" required>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-bold uppercase text-gray-500 mb-1">Email Address</label>
                            <input type="email" name="email" value="' . ($usr ? $usr->email : '') . '" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm outline-none" required>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-bold uppercase text-gray-500 mb-1">Country</label>
                            <select name="country" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 outline-none">
                                <option value="Indonesia">Indonesia</option>
                            </select>
                        </div>
                        
                        <div class="col-span-2 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Province</label>
                                <select name="province" id="nix_province" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm outline-none" required><option value="">Select Province...</option></select>
                                <input type="hidden" name="province_name" id="name_province">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">City / Regency</label>
                                <select name="city" id="nix_city" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm outline-none" required disabled><option value="">Select City...</option></select>
                                <input type="hidden" name="city_name" id="name_city">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">District</label>
                                <select name="district" id="nix_district" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm outline-none" required disabled><option value="">Select District...</option></select>
                                <input type="hidden" name="district_name" id="name_district">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Village</label>
                                <select name="village" id="nix_village" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm outline-none" required disabled><option value="">Select Village...</option></select>
                                <input type="hidden" name="village_name" id="name_village">
                            </div>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-xs font-bold uppercase text-gray-500 mb-1">Street Address</label>
                            <textarea name="address" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 outline-none" rows="2" required>' . ($usr ? $usr->addr : '') . '</textarea>
                        </div>
                    </div>
                </div>

                <!-- Shipping Method -->
                <div id="shipping-section" class="space-y-4 pt-4 border-t border-gray-100" style="display:none;">
                    <h2 class="text-xl font-bold text-gray-900 border-b border-gray-200 pb-2">Shipping Method</h2>
                    <div id="shipping-loader" class="hidden flex items-center gap-2 text-sm text-gray-500 py-2">
                        <div class="animate-spin rounded-full h-4 w-4 border-2 border-primary border-t-transparent"></div> Fetching shipping rates...
                    </div>
                    <div id="shipping-rates-list" class="space-y-2"></div>
                    <input type="hidden" name="shipping_courier" id="shipping_courier">
                    <input type="hidden" name="shipping_service" id="shipping_service">
                    <input type="hidden" name="shipping_cost" id="shipping_cost" value="0">
                    <input type="hidden" name="shipping_etd" id="shipping_etd">
                    <input type="hidden" name="shipping_engine" id="shipping_engine">
                </div>

                <div class="pt-6 border-t border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <a class="text-primary font-bold" href="' . Url::mod('cart') . '"><i class="bi bi-arrow-left me-1"></i> Return to cart</a>
                    <button type="submit" class="bg-primary text-on-primary px-8 py-4 rounded-full font-bold shadow-lg hover:opacity-90 transition">Place Order</button>
                </div>
            </form>
            ' . $js;
        } else {
            // Bootstrap HTML
            $html = '
            <form method="post" action="' . Url::mod('payment') . '" class="row g-4">
                <div class="col-12 border-bottom pb-2 mb-3 d-flex justify-content-between align-items-end">
                    <h2 class="h4 fw-bold mb-0">Contact Information</h2>
                    ' . (!$usr ? '
                        <span class="small text-muted">Already have an account? <a href="' . Url::login('backto=' . urlencode(Url::mod('checkout'))) . '">Log in</a></span>
                    ' : '
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2"><i class="bi bi-person-check-fill me-1"></i> Logged in as ' . ($usr->fname ?: Session::val('username')) . '</span>
                    ') . '
                </div>
                <!-- Standard Bootstrap 5 inputs -->
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted text-uppercase">Email Address</label>
                    <input class="form-control form-control-lg bg-light rounded-3 border-1 py-3 px-4 shadow-none" name="email" type="email" value="' . ($usr ? $usr->email : '') . '" required ' . ($usr ? 'readonly' : '') . '>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted text-uppercase">Phone Number</label>
                    <input class="form-control form-control-lg bg-light rounded-3 border-1 py-3 px-4 shadow-none" name="phone" type="text" value="' . (isset($usr->phone) ? $usr->phone : '') . '" required>
                </div>

                <div class="col-12 border-bottom pb-2 mb-3 mt-5">
                    <h2 class="h4 fw-bold mb-0">Shipping Address</h2>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted text-uppercase">First name</label>
                    <input name="fname" class="form-control form-control-lg bg-light rounded-3 border-1 py-3 px-4 shadow-none" type="text" value="' . ($usr ? $usr->fname : '') . '" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted text-uppercase">Last name</label>
                    <input name="lname" class="form-control form-control-lg bg-light rounded-3 border-1 py-3 px-4 shadow-none" type="text" value="' . ($usr ? $usr->lname : '') . '" required>
                </div>
                
                <div class="col-12">
                    <label class="form-label small fw-bold text-muted text-uppercase">Country</label>
                    <select name="country" class="form-select border-1 bg-light rounded-3 py-3 shadow-none">
                        <option value="Indonesia">Indonesia</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label small text-muted text-uppercase mb-1" style="font-size:10px; font-weight:bold;">Province</label>
                    <select name="province" id="nix_province" class="form-select border-1 bg-light rounded-3 shadow-none" required><option value="">Select...</option></select>
                    <input type="hidden" name="province_name" id="name_province">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted text-uppercase mb-1" style="font-size:10px; font-weight:bold;">City/Regency</label>
                    <select name="city" id="nix_city" class="form-select border-1 bg-light rounded-3 shadow-none" required disabled><option value="">Select...</option></select>
                    <input type="hidden" name="city_name" id="name_city">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted text-uppercase mb-1" style="font-size:10px; font-weight:bold;">District</label>
                    <select name="district" id="nix_district" class="form-select border-0 bg-light rounded-3 shadow-none" required disabled><option value="">Select...</option></select>
                    <input type="hidden" name="district_name" id="name_district">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted text-uppercase mb-1" style="font-size:10px; font-weight:bold;">Village</label>
                    <select name="village" id="nix_village" class="form-select border-0 bg-light rounded-3 shadow-none" required disabled><option value="">Select...</option></select>
                    <input type="hidden" name="village_name" id="name_village">
                </div>

                <div class="col-12">
                    <label class="form-label small fw-bold text-muted text-uppercase">Street Address</label>
                    <textarea name="address" class="form-control bg-light rounded-3 border-1 py-3 px-4 shadow-none" rows="2" required>' . ($usr ? $usr->addr : '') . '</textarea>
                </div>

                <div id="shipping-section" class="col-12 mt-4" style="display:none;">
                    <div class="border-bottom pb-2 mb-3"><h2 class="h4 fw-bold mb-0">Shipping Method</h2></div>
                    <div id="shipping-loader" class="d-none align-items-center gap-2 small text-muted py-2">
                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div> Fetching shipping rates...
                    </div>
                    <div id="shipping-rates-list" class="d-flex flex-column gap-2"></div>
                    <input type="hidden" name="shipping_courier" id="shipping_courier">
                    <input type="hidden" name="shipping_service" id="shipping_service">
                    <input type="hidden" name="shipping_cost" id="shipping_cost" value="0">
                    <input type="hidden" name="shipping_etd" id="shipping_etd">
                    <input type="hidden" name="shipping_engine" id="shipping_engine">
                </div>

                <div class="col-12 mt-5 pt-4 border-top d-flex justify-content-between align-items-center">
                    <a class="text-decoration-none fw-bold" href="' . Url::mod('cart') . '"><i class="bi bi-arrow-left me-1"></i> Return to cart</a>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow">Confirm & Place Order</button>
                </div>
            </form>
            ' . $js;
        }
        return $html;
    }

    public static function showSummary($framework = null, $total = 0, $taxAmount = 0, $items = [])
    {
        $currency = Options::v('nixomers_currency') ?: 'IDR';
        $taxRate = (float) Options::v('nixomers_tax') ?: 0;
        $grandTotal = $total + $taxAmount;

        // Unified Summary (uses gx- classes for cross-framework consistency)
        $itemsHtml = '';
        foreach ($items as $item) {
            $img = Posts::getPostImage($item['id']) ?: Site::$url . '/assets/images/noimage.png';
            $itemsHtml .= '
            <div class="gx-d-flex gx-items-center gx-gap-3 gx-mb-3 gx-pb-3">
                <div class="gx-position-relative gx-flex-shrink-0">
                    <img src="' . $img . '" class="gx-rounded-lg gx-object-fit-cover" style="width:64px;height:64px;border:1px solid #eee;">
                    <span class="gx-position-absolute gx-top-0 gx-start-100 gx-translate-middle gx-badge gx-rounded-pill gx-bg-dark gx-text-white gx-border gx-border-white" style="font-size:9px; padding: 2px 6px;">' . $item['qty'] . '</span>
                </div>
                <div class="gx-flex-grow-1 text-start">
                    <h6 class="gx-mb-0 gx-fw-bold gx-text-dark">' . $item['product']->title . '</h6>
                    <p class="gx-text-xs gx-text-muted gx-mb-0">' . $item['qty'] . ' x ' . $currency . ' ' . Nixomers::formatCurrency($item['price']) . '</p>
                </div>
                <div class="gx-text-end gx-ms-auto">
                    <span class="gx-fw-bold gx-text-dark">' . $currency . ' ' . Nixomers::formatCurrency($item['subtotal']) . '</span>
                </div>
            </div>';
        }

        return '
        <div class="gx-card gx-border-0 gx-shadow-sm gx-rounded-xl gx-p-4 gx-sticky-top nix-order-summary">
            <h5 class="gx-fw-bold gx-mb-4 gx-d-flex gx-items-center gx-gap-2">
                <i class="bi bi-card-list gx-text-primary"></i> Order Summary
            </h5>
            <div class="gx-mb-2">
                ' . $itemsHtml . '
            </div>
            <div class="gx-pt-3 gx-space-y-2 gx-border-top">
                <div class="gx-d-flex gx-justify-between gx-text-muted gx-mb-2">
                    <span>Subtotal</span>
                    <span class="gx-fw-bold gx-text-dark">' . $currency . ' ' . Nixomers::formatCurrency($total) . '</span>
                </div>
                <div class="gx-d-flex gx-justify-between gx-text-muted gx-mb-2" id="summary-shipping-row" style="display:none;">
                    <span>Shipping</span>
                    <span class="gx-fw-bold gx-text-dark" id="summary-shipping-cost">' . $currency . ' 0</span>
                </div>
                <div class="gx-d-flex gx-justify-between gx-text-muted gx-mb-2">
                    <span>Tax (' . $taxRate . '%)</span>
                    <span class="gx-fw-bold gx-text-dark">' . $currency . ' ' . Nixomers::formatCurrency($taxAmount) . '</span>
                </div>
                <hr class="gx-my-4">
                <div class="gx-d-flex gx-justify-between gx-items-center gx-mb-4">
                    <span class="gx-h5 gx-fw-bold gx-mb-0">Total</span>
                    <div class="gx-text-end">
                        <span class="gx-h3 gx-fw-black gx-text-primary gx-d-block gx-mb-0" id="summary-grand-total">' . $currency . ' ' . Nixomers::formatCurrency($grandTotal) . '</span>
                        <small class="gx-text-muted gx-text-uppercase gx-fw-bold" style="font-size:10px;">Final amount</small>
                    </div>
                </div>
                <div class="gx-p-3 gx-bg-light gx-text-muted gx-border gx-border-light gx-rounded-lg gx-d-flex gx-items-start gx-gap-3">
                    <i class="bi bi-shield-check gx-text-primary gx-h4 gx-mb-0"></i>
                    <p class="gx-text-xs gx-leading-relaxed gx-mb-0 gx-text-start">Secure and verified payment gateway system. Your data is encrypted.</p>
                </div>
            </div>
        </div>';
    }

    private static function injectScripts($total = 0, $taxAmount = 0)
    {
        $currency = Options::v('nixomers_currency') ?: 'IDR';
        return '
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const selCountry = document.querySelector("select[name=\'country\']");
                const selProv = document.getElementById("nix_province");
                const selCity = document.getElementById("nix_city");
                const selDist = document.getElementById("nix_district");
                const selVill = document.getElementById("nix_village");

                async function fetchRegions(type, parentId = "") {
                    try {
                        const q = (type === "province") ? "Indonesia" : "";
                        const res = await fetch("' . Url::ajax('nixomers', ['action' => 'shipping_regions']) . '&type=" + type + "&parent=" + parentId + "&q=" + q);
                        const data = await res.json();
                        if (data.is_success) {
                            return data.data;
                        }
                    } catch (e) { console.error(e); }
                    return [];
                }

                if (selProv) {
                    selProv.addEventListener("change", async function() {
                        selCity.innerHTML = \'<option value="">Loading...</option>\';
                        selCity.disabled = true;
                        selDist.innerHTML = \'<option value="">Select...</option>\';
                        selVill.innerHTML = \'<option value="">Select...</option>\';
                        
                        const regions = await fetchRegions("city", this.value);
                        selCity.innerHTML = \'<option value="">Select City/Regency...</option>\';
                        regions.forEach(r => {
                            const opt = document.createElement("option");
                            opt.value = r.id;
                            opt.text = r.name;
                            opt.dataset.name = r.name;
                            selCity.appendChild(opt);
                        });
                        selCity.disabled = false;
                    });
                }
                
                if (selCity) {
                    selCity.addEventListener("change", async function() {
                        selDist.innerHTML = \'<option value="">Loading...</option>\';
                        selDist.disabled = true;
                        selVill.innerHTML = \'<option value="">Select...</option>\';
                        
                        const regions = await fetchRegions("district", this.value);
                        selDist.innerHTML = \'<option value="">Select District...</option>\';
                        regions.forEach(r => {
                            const opt = document.createElement("option");
                            opt.value = r.id;
                            opt.text = r.name;
                            opt.dataset.name = r.name;
                            selDist.appendChild(opt);
                        });
                        selDist.disabled = false;
                    });
                }

                if (selDist) {
                    selDist.addEventListener("change", async function() {
                        selVill.innerHTML = \'<option value="">Loading...</option>\';
                        selVill.disabled = true;
                        
                        const regions = await fetchRegions("village", this.value);
                        selVill.innerHTML = \'<option value="">Select Village...</option>\';
                        regions.forEach(r => {
                            const opt = document.createElement("option");
                            opt.value = r.id;
                            opt.text = r.name;
                            opt.dataset.name = r.name;
                            selVill.appendChild(opt);
                        });
                        selVill.disabled = false;
                    });
                }

                // Initial load of provinces
                (async () => {
                    const provinces = await fetchRegions("province");
                    if (selProv) {
                        selProv.innerHTML = \'<option value="">Select Province...</option>\';
                        provinces.forEach(p => {
                            const opt = document.createElement("option");
                            opt.value = p.id;
                            opt.text = p.name;
                            opt.dataset.name = p.name;
                            selProv.appendChild(opt);
                        });
                    }
                })();

                let shippingTimer;
                function fetchShippingRates(villageId) {
                    const shipSec = document.getElementById("shipping-section");
                    const loader = document.getElementById("shipping-loader");
                    const list = document.getElementById("shipping-rates-list");
                    
                    if (!shipSec || !villageId) return;
                    
                    shipSec.style.display = "block";
                    loader.style.display = "flex";
                    list.innerHTML = "";
                    
                    clearTimeout(shippingTimer);
                    shippingTimer = setTimeout(() => {
                        fetch("' . Url::ajax('nixomers', ['action' => 'shipping_rates']) . '&village_id=" + villageId)
                        .then(res => res.json())
                        .then(res => {
                            loader.style.display = "none";
                            if (res.status && res.data) {
                                let html = "";
                                const locale = ("' . $currency . '" === "IDR" || "' . $currency . '" === "Rp") ? "id-ID" : "en-US";
                                res.data.forEach(service => {
                                    const id = service.courier_name + "-" + service.service_name;
                                        html += `
                                        <label class="gx-d-flex gx-items-center gx-gap-3 gx-p-3 gx-border gx-rounded-lg gx-mb-2 gx-cursor-pointer hover:gx-bg-light gx-transition gx-w-100 nix-shipping-option">
                                            <input type="radio" name="shipping_option" value="${id}" class="gx-form-check-input" required 
                                                data-cost="${service.cost}" 
                                                data-courier="${service.courier_name}" 
                                                data-service="${service.service_name}" 
                                                data-etd="${service.etd}" 
                                                data-engine="${service.engine}">
                                            <div class="gx-flex-grow-1">
                                                <div class="gx-d-flex gx-justify-between gx-items-center gx-mb-1 gx-w-100">
                                                    <span class="gx-fw-bold gx-text-dark">${service.courier_name} - ${service.service_name}</span>
                                                    <span class="gx-fw-black gx-text-primary">' . $currency . ' ${new Intl.NumberFormat(locale).format(service.cost)}</span>
                                                </div>
                                                <p class="gx-text-xs gx-text-muted gx-fw-semibold gx-m-0 gx-text-left">Estimate: ${service.etd} Days</p>
                                            </div>
                                        </label>`;
                                });
                                list.innerHTML = html;

                                // Add change event to shipping options
                                list.querySelectorAll(\'input[name="shipping_option"]\').forEach(radio => {
                                    radio.addEventListener("change", function() {
                                        const cost = parseFloat(this.dataset.cost);
                                        document.getElementById("shipping_cost").value = cost;
                                        document.getElementById("shipping_courier").value = this.dataset.courier;
                                        document.getElementById("shipping_service").value = this.dataset.service;
                                        document.getElementById("shipping_etd").value = this.dataset.etd;
                                        document.getElementById("shipping_engine").value = this.dataset.engine;
                                        
                                        // Update Summary
                                        const sumShipRow = document.getElementById("summary-shipping-row");
                                        const sumShipCost = document.getElementById("summary-shipping-cost");
                                        const sumGrandTotal = document.getElementById("summary-grand-total");
                                        const locale = ("' . $currency . '" === "IDR" || "' . $currency . '" === "Rp") ? "id-ID" : "en-US";
                                        
                                        if (sumShipRow) sumShipRow.style.display = "flex";
                                        if (sumShipCost) sumShipCost.innerText = "' . $currency . ' " + new Intl.NumberFormat(locale).format(cost);
                                        
                                        const subtotal = ' . (float) $total . ';
                                        const tax = ' . (float) $taxAmount . ';
                                        const total = subtotal + tax + cost;
                                        if (sumGrandTotal) sumGrandTotal.innerText = "' . $currency . ' " + new Intl.NumberFormat(locale).format(total);
                                    });
                                });
                            } else {
                                list.innerHTML = \'<div class="gx-text-sm gx-text-danger gx-py-2">Error: \' + res.message + \'</div>\';
                            }
                        })
                        .catch(err => {
                            loader.style.display = "none";
                            list.innerHTML = \'<div class="gx-text-sm gx-text-danger gx-py-2">Failed to connect to shipping service.</div>\';
                        });
                    }, 500); 
                }

                if (selVill) {
                    selVill.addEventListener("change", function() {
                        const isID = selCountry ? selCountry.value === "Indonesia" : true;
                        if (isID && this.value) {
                            fetchShippingRates(this.value);
                        }
                    });
                }

                document.querySelector("form.nixomers-checkout-form")?.addEventListener("submit", function(e) {
                    if(selProv) document.getElementById("name_province").value = selProv.options[selProv.selectedIndex]?.dataset.name || "";
                    if(selCity) document.getElementById("name_city").value = selCity.options[selCity.selectedIndex]?.dataset.name || "";
                    if(selDist) document.getElementById("name_district").value = selDist.options[selDist.selectedIndex]?.dataset.name || "";
                    if(selVill) document.getElementById("name_village").value = selVill.options[selVill.selectedIndex]?.dataset.name || "";
                });
            });
        </script>';
    }
}
