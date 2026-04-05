<!-- Category Header -->
<header class="header-py text-center border-b border-outline-variant/10 pb-16">
    <span class="font-label text-xs uppercase tracking-[0.2em] text-primary mb-4 font-bold inline-block">Category Archive</span>
    <h1 class="font-headline text-5xl md:text-7xl font-bold tracking-tighter text-on-surface mb-6">
        {Categories::name($cat)}
    </h1>
    {var $cat_desc = Themusk::getCategoryDesc($cat)}
    {if $cat_desc}
        <p class="font-body italic text-xl md:text-2xl text-on-surface-variant max-w-2xl mx-auto leading-relaxed">
            {$cat_desc}
        </p>
    {/if}
</header>

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
                
                <h3 class="font-headline text-2xl font-bold text-on-surface mb-4 group-hover:text-primary transition-colors">
                    <a href="{Url::post($p->id)}">
                        {Hooks::filter('post_title_filter', $p->title)|noescape}
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
            <h3 class="text-xl font-bold text-on-surface">No Posts Found in this Category</h3>
        </div>
    {/if}
</section>

<!-- Pagination -->
<div class="mt-24 flex justify-center pb-24">
    {$paging|noescape}
</div>
