<!-- Hero Section -->
<header class="header-py text-center">
    <h1 class="font-headline text-5xl md:text-7xl font-bold tracking-tighter text-on-surface mb-6">
        {Themusk::opt('intro_title')}
    </h1>
    <p class="font-body italic text-xl md:text-2xl text-on-surface-variant max-w-2xl mx-auto">
        {Themusk::opt('intro_text')}
    </p>
</header>

<!-- Featured Post: Bento-Style Asymmetric Layout Slideshow -->
{var $feat = Themusk::opt('featured_posts')}
{if $feat}
    {var $f_ids = array_filter(array_map('trim', explode(',', $feat)))}
    {if count($f_ids) > 0}
        <section class="mb-32">
            <div class="swiper featured-swiper overflow-hidden rounded-xl bg-surface-container-lowest shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-outline-variant/10">
                <div class="swiper-wrapper">
                    {foreach $f_ids as $f_id}
                        {var $p_feat = Db::result("SELECT * FROM `posts` WHERE `id` = ? LIMIT 1", [$f_id])}
                        {if is_array($p_feat) && !isset($p_feat['error'])}
                            {var $fp = $p_feat[0]}
                            {var $fp_img_raw = Posts::getPostImage($fp->id) ?: Themusk::getImage($fp->content)}
                            {var $fp_img = $fp_img_raw ? Url::thumb($fp_img_raw, 'full') : ''}
                            <div class="swiper-slide list-none h-auto">
                                <div class="group relative grid grid-cols-1 lg:grid-cols-12 gap-0 transition-opacity duration-300">
                                    <div class="lg:col-span-7 aspect-[16/10] overflow-hidden bg-surface-container-high">
                                        <a href="{Url::post($fp->id)}">
                                            <img alt="Featured post image" class="w-full h-full object-cover transition-transform duration-[1.2s] group-hover:scale-105" src="{if $fp_img}{$fp_img}{else}{Themusk::opt('default_image')}{/if}" />
                                        </a>
                                    </div>
                                    <div class="lg:col-span-5 p-8 lg:p-12 flex flex-col justify-center bg-surface-container-lowest overflow-hidden">
                                        <span class="font-label text-xs uppercase tracking-widest text-primary mb-4 font-bold">Featured Essay</span>
                                        <h2 class="font-headline text-3xl lg:text-5xl font-extrabold tracking-tight text-on-surface mb-6 leading-tight">
                                            <a href="{Url::post($fp->id)}" class="hover:text-primary transition-colors">
                                                {Typo::Xclean($fp->title)}
                                            </a>
                                        </h2>
                                        <div class="font-body text-lg text-on-surface-variant mb-8 leading-relaxed line-clamp-4">
                                            {var $fp_content = Posts::content($fp->content)}
                                            {str_replace('&nbsp;', ' ', ($fp_content|stripHtml))|truncate:200|noescape}
                                        </div>
                                        <div class="flex items-center">
                                            <a href="{Url::post($fp->id)}" class="bg-primary text-on-primary px-8 py-3 rounded-md font-label text-sm font-semibold hover:opacity-90 transition-all shadow-sm inline-block">
                                                Read Essay
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/if}
                    {/foreach}
                </div>
                <!-- Navigation -->
                <div class="swiper-button-prev !text-primary !w-12 !h-12 bg-white/80 rounded-full backdrop-blur after:!text-lg opacity-0 hover:opacity-100 transition-opacity hidden md:flex" style="left:2rem;"></div>
                <div class="swiper-button-next !text-primary !w-12 !h-12 bg-white/80 rounded-full backdrop-blur after:!text-lg opacity-0 hover:opacity-100 transition-opacity hidden md:flex" style="right:2rem;"></div>
            </div>
            <div class="swiper-pagination !relative !bottom-0 mt-6 !text-primary"></div>
        </section>
    {/if}
{/if}

<!-- Signature Quote Component -->
{var $q_text = Themusk::opt('quote_text')}
{if $q_text}
<section class="mb-32 max-w-3xl mx-auto text-center">
    <div class="editorial-quote-line text-left md:inline-block">
        <blockquote class="font-body italic text-3xl text-primary leading-snug">
            "{$q_text}"
        </blockquote>
        {var $q_author = Themusk::opt('quote_author')}
        {if $q_author}
            <cite class="block mt-4 font-label text-xs uppercase tracking-widest text-on-surface-variant font-bold">— {$q_author}</cite>
        {/if}
    </div>
</section>
{/if}

<!-- Latest Posts Header -->
<div class="flex items-baseline justify-between mb-12 border-b border-outline-variant/10 pb-4">
    <h3 class="font-headline text-2xl font-black uppercase tracking-widest text-on-surface">Latest Posts</h3>
</div>

<!-- Posts Grid -->
<section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
    {if $posts}
        {foreach $posts as $p}
            <article class="flex flex-col group">
                <a href="{Url::post($p->id)}" class="block">
                    <div class="aspect-[4/3] mb-6 overflow-hidden rounded-lg bg-surface-container-low relative">
                        {var $p_img_raw = Posts::getPostImage($p->id) ?: Themusk::getImage($p->content)}
                        {var $p_img = $p_img_raw ? Url::thumb($p_img_raw, 'large') : ''}
                        <img alt="Post thumbnail" class="w-full h-full object-cover grayscale transition-all duration-500 group-hover:grayscale-0 group-hover:scale-110" src="{if $p_img}{$p_img}{else}{Themusk::opt('default_image')}{/if}" />
                    </div>
                </a>
                <div class="flex flex-wrap gap-2 mb-4">
                    {if $p->cat}
                        <a href="{Url::cat($p->cat)}" class="px-3 py-1 bg-secondary-fixed-dim text-on-secondary-fixed text-[10px] font-bold font-label uppercase tracking-widest rounded-full hover:opacity-80 transition-opacity">{Categories::name($p->cat)}</a>
                    {else}
                        <span class="px-3 py-1 bg-secondary-fixed-dim text-on-secondary-fixed text-[10px] font-bold font-label uppercase tracking-widest rounded-full">Uncategorized</span>
                    {/if}
                </div>
                <h3 class="font-headline text-2xl font-bold text-on-surface mb-4 group-hover:text-primary transition-colors">
                    <a href="{Url::post($p->id)}">
                        {Typo::Xclean($p->title)}
                    </a>
                </h3>
                <p class="font-body text-on-surface-variant leading-relaxed mb-6 line-clamp-3">
                    {Posts::content($p->content)|stripHtml|truncate:200|noescape}
                </p>
                <div class="mt-auto pt-4 flex items-center text-outline text-xs font-label uppercase tracking-widest font-semibold border-t border-outline-variant/10">
                    <span>{Date::format($p->date, 'M d, Y')}</span>
                </div>
            </article>
        {/foreach}
    {else}
        <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-12">
            <h3 class="text-xl font-bold text-on-surface">No Posts Found</h3>
        </div>
    {/if}
</section>

<!-- Pagination/Load More -->
<div class="mt-24 flex justify-center">
    {$paging|noescape}
</div>

{if (Themusk::opt('adsense') ?? '') != ''}
    <div class="mt-12 text-center">
        {Themusk::opt('adsense')|noescape}
    </div>
{/if}

