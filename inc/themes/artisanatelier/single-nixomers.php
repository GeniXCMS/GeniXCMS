<section class="relative pt-32 pb-20 overflow-hidden bg-surface-container-low">
    <div class="absolute inset-0 z-0 opacity-10 pointer-events-none"
        style="background-image: radial-gradient({ArtisanAtelier::opt('primary_color')} 0.5px, transparent 0.5px); background-size: 40px 40px;">
    </div>

    <div class="max-w-screen-2xl mx-auto px-12 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-start">
            <!-- Product Visual Gallery -->
            <div class="relative group">
                <div class="absolute -top-10 -left-10 w-64 h-64 bg-primary/10 blob-mask-1 -z-10 animate-pulse"></div>
                <div class="absolute -bottom-10 -right-10 w-80 h-80 bg-secondary/10 blob-mask-2 -z-10"></div>

                <div
                    class="aspect-square rounded-3xl overflow-hidden shadow-[0_48px_80px_-16px_rgba(57,56,50,0.15)] bg-white border border-outline-variant/5">
                    {var $img = Posts::getPostImage($posts[0]->id)}
                    <img src="{(empty($img) ? ArtisanAtelier::opt('hero_image') : Url::thumb($img))}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-1000"
                        alt="{$title|stripHtml}">
                </div>
            </div>

            <!-- Product Details -->
            <div class="flex flex-col">
                <nav
                    class="flex items-center gap-3 text-sm font-semibold uppercase tracking-widest text-secondary mb-8">
                    <a href="{Url::mod('store')}" class="hover:text-primary transition-colors">Atelier Catalog</a>
                    <span class="text-stone-300">/</span>
                    <span class="text-on-surface">{$category_name}</span>
                </nav>

                <h1 class="text-7xl font-serif font-bold text-on-surface leading-[1.1] tracking-tight mb-6">
                    {$title|stripHtml}
                </h1>

                <div class="flex items-center gap-6 mb-10">
                    <div class="text-4xl font-bold text-primary font-serif">
                        {Options::v('nixomers_currency') ?: 'IDR'} {number_format((float)Posts::getParam('price',
                        $posts[0]->id), 0, ',', '.')}
                    </div>
                    <div class="h-8 w-[1px] bg-stone-200"></div>
                    <div
                        class="flex items-center gap-2 px-4 py-1.5 bg-secondary/10 text-secondary rounded-full text-sm font-bold">
                        <span class="material-symbols-outlined text-lg">inventory_2</span>
                        {(int)Posts::getParam('stock', $posts[0]->id)} Units Available
                    </div>
                </div>

                <div class="prose prose-stone lg:prose-xl max-w-none artisan-content mb-12">
                    <div class="text-on-surface-variant leading-relaxed text-lg">
                        {Posts::content($posts[0]->content)|noescape}
                    </div>
                </div>

                <!-- Purchase Card -->
                <div class="bg-white p-10 rounded-3xl border border-outline-variant/20 shadow-xl shadow-stone-100/50">
                    <div class="grid grid-cols-2 gap-8 mb-10">
                        <div class="space-y-1">
                            <span
                                class="text-[10px] font-black uppercase tracking-[0.2em] text-stone-400 block">Origin</span>
                            <span class="text-on-surface font-serif font-bold italic">Handcrafted in Atelier</span>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-stone-400 block">SKU
                                Reference</span>
                            <span class="text-on-surface font-mono font-medium">#{Posts::getParam('sku', $posts[0]->id)
                                ?: 'AA-'.strtoupper(substr($posts[0]->slug, 0, 4))}</span>
                        </div>
                    </div>

                    <form method="post" action="{Url::mod('cart')}" class="flex flex-col sm:flex-row gap-5">
                        <div
                            class="flex items-center justify-between bg-surface-container rounded-2xl px-8 py-3 border border-outline-variant/10 min-w-[160px]">
                            <button type="button" onclick="this.nextElementSibling.stepDown()"
                                class="text-2xl font-bold text-on-surface hover:text-primary transition-colors">−</button>
                            <input type="number" name="qty" value="1" min="1"
                                max="{(int)Posts::getParam('stock', $posts[0]->id)}"
                                class="w-12 text-center bg-transparent border-0 focus:ring-0 font-bold text-xl text-on-surface">
                            <button type="button" onclick="this.previousElementSibling.stepUp()"
                                class="text-2xl font-bold text-on-surface hover:text-primary transition-colors">+</button>
                        </div>

                        <input type="hidden" name="product_id" value="{$posts[0]->id}">
                        <input type="hidden" name="nix_action" value="add">

                        <button type="submit"
                            class="flex-1 bg-on-surface text-surface hover:bg-primary px-10 py-5 rounded-2xl font-bold text-lg shadow-2xl shadow-stone-300 transition-all active:scale-[0.98] flex items-center justify-center gap-3">
                            <span class="material-symbols-outlined">shopping_bag</span>
                            Add to Selection
                        </button>
                    </form>
                </div>

                <!-- Product Details Accordion/List -->
                <div class="mt-12 space-y-8">
                    {var $material = Posts::getParam('material', $posts[0]->id)}
                    {var $length = Posts::getParam('length', $posts[0]->id)}
                    {var $width = Posts::getParam('width', $posts[0]->id)}
                    {var $height = Posts::getParam('height', $posts[0]->id)}
                    {var $weight = Posts::getParam('weight', $posts[0]->id)}
                    {var $note = Posts::getParam('note', $posts[0]->id)}

                    <div class="border-b border-outline-variant/20 pb-8">
                        <h3 class="text-xl text-secondary font-bold mb-6 font-serif uppercase tracking-widest">
                            Specifications</h3>
                        <dl class="grid grid-cols-2 gap-y-4 text-sm">
                            {if $material}
                            <dt class="text-on-surface-variant italic">Material</dt>
                            <dd class="text-on-surface font-bold text-right">{$material}</dd>
                            {/if}

                            {if $length || $width || $height}
                            <dt class="text-on-surface-variant italic">Dimensions (L x W x H)</dt>
                            <dd class="text-on-surface font-bold text-right">
                                {$length ?: '0'} x {$width ?: '0'} {if $height} x {$height} {/if} cm
                            </dd>
                            {/if}

                            {if $weight}
                            <dt class="text-on-surface-variant italic">Weight</dt>
                            <dd class="text-on-surface font-bold text-right">{$weight} grams</dd>
                            {/if}
                        </dl>
                    </div>

                    {if $note}
                    <div class="border-b border-outline-variant/20 pb-8">
                        <h3 class="text-xl text-secondary font-bold mb-6 font-serif uppercase tracking-widest">Care
                            Instructions</h3>
                        <p class="text-base text-on-surface-variant leading-relaxed italic">
                            {$note}
                        </p>
                    </div>
                    {/if}

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-1">
                        <div class="flex items-center gap-4 text-sm text-stone-500 italic">
                            <div
                                class="w-10 h-10 rounded-full bg-surface-container flex-shrink-0 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-xl">verified</span>
                            </div>
                            Authentication Guaranteed
                        </div>
                        <div class="flex items-center gap-4 text-sm text-stone-500 italic">
                            <div
                                class="w-10 h-10 rounded-full bg-surface-container flex-shrink-0 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-xl">local_shipping</span>
                            </div>
                            Bespoke Packaging & Logistics
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Products / Ecosystem Section -->
<section class="py-32 bg-white">
    <div class="max-w-screen-2xl mx-auto px-12">
        <div class="flex flex-col md:flex-row justify-between items-end mb-20">
            <div>
                <span class="inline-block label-md all-caps tracking-[0.2em] text-primary mb-4 font-label font-bold">03
                    // COMPLETION</span>
                <h2 class="text-6xl font-serif font-bold text-on-surface tracking-tight">Pairs Beautifully With</h2>
            </div>
            <a href="{Url::mod('store')}"
                class="text-on-surface-variant hover:text-primary transition-colors flex items-center gap-2 font-bold uppercase tracking-widest text-xs mb-2">
                View Full Collection <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
            {var $related = Query::table('posts')->where('type', 'nixomers')->where('status', '1')->where('id', '!=',
            $posts[0]->id)->limit(3)->get()}
            {var $related = Posts::prepare($related)}
            {if !empty($related) && !isset($related['error'])}
            {foreach $related as $r}
            <div class="group">
                <a href="{Url::post($r->id)}" class="block">
                    <div
                        class="aspect-[4/5] rounded-3xl overflow-hidden mb-8 shadow-sm group-hover:shadow-xl transition-all duration-700 bg-surface-container">
                        {var $ritem_img = Posts::getPostImage($r->id)}
                        <img src="{(empty($ritem_img) ? ArtisanAtelier::opt('hero_image') : Url::thumb($ritem_img))}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-1000">
                    </div>
                    <h4 class="text-2xl font-serif font-bold text-on-surface mb-2">{$r->title}</h4>
                    <div class="text-primary font-bold">{Options::v('nixomers_currency') ?: 'IDR'}
                        {number_format((float)Posts::getParam('price', $r->id), 0, ',', '.')}</div>
                </a>
            </div>
            {/foreach}
            {else}
            <div class="col-span-full py-12 text-center text-stone-400 italic">
                More pieces arriving soon at the atelier.
            </div>
            {/if}
        </div>
    </div>
</section>