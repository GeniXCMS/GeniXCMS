<?php
/**
 * NixCatalog Class
 * Handles Store Catalog rendering and AJAX filtering
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

class NixCatalog
{
    /**
     * Main Catalog Renderer
     */
    public static function render()
    {
        $cat = Typo::int($_GET['cat'] ?? 0);

        $paging = isset($_GET['paging']) ? Typo::int($_GET['paging']) : 1;
        $max = Options::v('post_perpage');
        $offset = ($paging > 1) ? ($paging - 1) * $max : 0;

        $query = Query::table('posts')->where('type', 'nixomers')->where('status', '1');
        if ($cat > 0) {
            $query->where('cat', $cat);
        }
        $posts = $query->orderBy('date', 'DESC')->limit($max, $offset)->get();

        // Enrich posts with parameters for theme templates
        if (!empty($posts)) {
            foreach ($posts as &$p) {
                $p->price = Posts::getParam('price', $p->id) ?: '0';
                $p->sku = Posts::getParam('sku', $p->id) ?: '-';
                $p->image = Posts::getPostImage($p->id);
            }
        }

        $categories = Query::table('cat')->where('type', 'nixomers')->orderBy('name', 'ASC')->get();

        $where = "`type` = 'nixomers' AND `status` = '1'";
        if ($cat > 0) {
            $where .= " AND `cat` = '{$cat}'";
        }
        $url = Url::mod('store');
        if (SMART_URL) {
            $url .= '?';
            if ($cat > 0) {
                $url .= 'cat=' . $cat;
            }
        } else {
            if ($cat > 0) {
                $url .= '&cat=' . $cat;
            }
        }

        $paging_arr = [
            'paging' => $paging,
            'table' => 'posts',
            'where' => $where,
            'max' => $max,
            'url' => $url,
            'type' => Options::v('pagination')
        ];
        // Force non-smart appends (&paging=) to avoid stripping the '.html' URL suffix 
        $paging_html = Paging::create($paging_arr, false);

        // Theme Override
        $themeOut = Nixomers::renderThemeView('catalog', [
            'posts' => $posts,
            'categories' => $categories,
            'active_cat' => $cat,
            'materials' => Nixomers::getMaterials(),
            'paging' => $paging_html
        ]);
        if ($themeOut !== false)
            return $themeOut;

        $currency = Options::v('nixomers_currency') ?: 'IDR';
        $framework = Nixomers::getFramework();

        if ($framework === 'tailwindcss') {
            $html = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">';
            if (!empty($posts)) {
                foreach ($posts as $p) {
                    $price = Posts::getParam('price', $p->id) ?: '0';
                    $sku = Posts::getParam('sku', $p->id) ?: '-';
                    $img = Posts::getPostImage($p->id) ?: Site::$url . '/assets/images/noimage.png';

                    $html .= '
                    <div class="w-full">
                        <div class="bg-white border border-gray-100 shadow-sm rounded-3xl h-full overflow-hidden flex flex-col transition-transform hover:scale-[1.02]">
                            <a href="' . Url::post($p->id) . '">
                                <img src="' . $img . '" class="w-full object-cover" alt="' . $p->title . '" style="height:250px;">
                            </a>
                            <div class="p-6 text-center flex-grow">
                                <h5 class="text-xl font-bold mb-1"><a href="' . Url::post($p->id) . '" class="text-gray-900 no-underline hover:text-blue-600">' . $p->title . '</a></h5>
                                <p class="text-gray-500 text-sm mb-2">SKU: ' . $sku . '</p>
                                <h4 class="text-blue-600 text-2xl font-bold mb-4">' . $currency . ' ' . Nixomers::formatCurrency($price) . '</h4>
                                <form method="post">
                                    <input type="hidden" name="product_id" value="' . $p->id . '">
                                    <input type="hidden" name="nix_action" value="add">
                                    <button type="submit" class="w-full border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white font-bold py-2 px-4 rounded-full transition-colors flex items-center justify-center gap-2">
                                        <i class="bi bi-cart-plus"></i> Add to Cart
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>';
                }
            } else {
                $html .= '<div class="col-span-full text-center py-20 text-gray-400"><i class="bi bi-box2 text-6xl block mb-4 opacity-25"></i><h5 class="text-xl">No products available.</h5></div>';
            }
            $html .= '</div>';
        } else {
            $html = '<div class="row g-4">';
            if (!empty($posts)) {
                foreach ($posts as $p) {
                    $price = Posts::getParam('price', $p->id) ?: '0';
                    $sku = Posts::getParam('sku', $p->id) ?: '-';
                    $img = Posts::getPostImage($p->id) ?: Site::$url . '/assets/images/noimage.png';

                    $html .= '
                    <div class="col-md-4 col-sm-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 product-card overflow-hidden">
                            <a href="' . Url::post($p->id) . '">
                                <img src="' . $img . '" class="card-img-top" alt="' . $p->title . '" style="height:250px; object-fit:cover;">
                            </a>
                            <div class="card-body p-4 text-center">
                                <h5 class="fw-bold mb-1"><a href="' . Url::post($p->id) . '" class="text-dark text-decoration-none">' . $p->title . '</a></h5>
                                <p class="text-muted small mb-2">SKU: ' . $sku . '</p>
                                <h4 class="text-primary fw-bold mb-3">' . $currency . ' ' . Nixomers::formatCurrency($price) . '</h4>
                                <form method="post">
                                    <input type="hidden" name="product_id" value="' . $p->id . '">
                                    <input type="hidden" name="nix_action" value="add">
                                    <button type="submit" class="btn btn-outline-primary rounded-pill w-100 fw-bold px-4 py-2">
                                        <i class="bi bi-cart-plus me-2"></i> Add to Cart
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>';
                }
            } else {
                $html .= '<div class="col-12 text-center py-5 text-muted"><i class="bi bi-box2 fs-1 d-block mb-3 opacity-25"></i><h5>No products available.</h5></div>';
            }
            $html .= '</div>';
        }
        return $html;
    }

    /**
     * AJAX Catalog search/filter handler
     */
    public static function ajaxCatalog()
    {
        $cat = Typo::int($_GET['cat'] ?? 0);
        $material = isset($_GET['material']) ? Typo::cleanX($_GET['material']) : '';
        $priceRange = isset($_GET['price']) ? Typo::cleanX($_GET['price']) : '';

        $query = Query::table('posts')->where('type', 'nixomers')->where('status', '1');

        if ($cat > 0) {
            $query->where('cat', $cat);
        }

        // Filter by Material
        if ($material != '') {
            $mats = Query::table('posts_param')
                ->where('param', 'material')
                ->where('value', $material)
                ->get();
            $matIds = array_column($mats, 'post_id');
            $query->whereIn('id', $matIds);
        }

        // Filter by Price Range
        if ($priceRange != '') {
            $pq = Query::table('posts_param')->where('param', 'price');
            switch ($priceRange) {
                case '1':
                    $pq->where('value', '<', 100000);
                    break;
                case '2':
                    $pq->where('value', '>=', 100000)->where('value', '<=', 500000);
                    break;
                case '3':
                    $pq->where('value', '>', 500000)->where('value', '<=', 1000000);
                    break;
                case '4':
                    $pq->where('value', '>', 1000000);
                    break;
            }
            $prices = $pq->get();
            $priceIds = array_column($prices, 'post_id');
            $query->whereIn('id', $priceIds);
        }

        $posts = $query->orderBy('date', 'DESC')->get();

        $themeOut = Nixomers::renderThemeView('ajax-catalog', [
            'posts' => $posts
        ]);

        if ($themeOut !== false) {
            header('Content-Type: text/html');
            echo $themeOut;
            exit;
        }
        exit;
    }
}
