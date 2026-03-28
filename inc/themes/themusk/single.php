{if $posts}
    {foreach $posts as $p}
    <article class="header-py">
        {Hooks::run('post_header_action', ['posts' => [$p], 'website_lang' => $website_lang])|noescape}
        <header class="post-content-max-w mx-auto mb-12 text-center px-6">
            {if $p->cat}
                <a href="{Url::cat($p->cat)}" class="font-label text-xs uppercase tracking-widest text-primary mb-4 font-bold inline-block hover:opacity-80 transition-opacity">{Categories::name($p->cat)}</a>
            {/if}
            <h1 class="font-headline text-4xl md:text-6xl font-bold tracking-tighter text-on-surface mb-6 leading-tight">
                {Hooks::filter('post_title_filter', $p->title)|noescape}
            </h1>
            <div class="font-label text-sm uppercase tracking-widest text-outline">
                {Date::format($p->date, 'M d, Y')} • {if $p->author}{$p->author}{else}Author{/if}
            </div>
        </header>

        {var $p_img_raw = Posts::getPostImage($p->id) ?: Themusk::getImage($p->content)}
        {if $p_img_raw}
        {var $p_img = Url::thumb($p_img_raw, 'full')}
        <div class="max-w-5xl lg:max-w-6xl mx-auto mb-16 overflow-hidden rounded-2xl shadow-xl border border-black/5 px-4 md:px-0">
            <img src="{$p_img}" class="w-full h-auto object-cover max-h-[800px]" alt="{$p->title}">
        </div>
        {/if}
        
        <div class="post-content-max-w mx-auto px-6 font-body text-xl prose-p:mb-8 prose-headings:font-headline prose-headings:font-bold prose-headings:mb-6 prose-headings:mt-12 prose-slate text-on-surface-variant leading-relaxed text-left">
            {var $p_content = Posts::content($p->content)}
            {* remove first image from content if it matches the featured image to avoid duplication *}
            {if $p_img_raw}
                {var $p_content = preg_replace('/<img .*?src=[\'"]'.preg_quote($p_img_raw, '/').'[\'"].*?>/i', '', $p_content, 1)}
            {/if}
            
            {Hooks::run('post_content_before_action', ['posts' => [$p], 'website_lang' => $website_lang])|noescape}
            {$p_content|noescape}
            {Hooks::run('post_content_after_action', ['posts' => [$p], 'website_lang' => $website_lang])|noescape}
        </div>
        {Hooks::run('post_footer_action', ['posts' => [$p], 'website_lang' => $website_lang])|noescape}
    </article>

    <div class="py-24 border-t border-outline-variant/10">
        <h4 class="font-headline text-2xl font-black uppercase tracking-widest text-on-surface mb-12">Related Essays</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
            {var $randFn = (defined('DB_DRIVER') && DB_DRIVER === 'mysql') ? 'RAND()' : 'RANDOM()'}
            {var $related = Db::result("SELECT * FROM `posts` WHERE `cat` = '$p->cat' AND `id` != '$p->id' AND `status` = '1' ORDER BY $randFn LIMIT 3")}
            {if is_array($related) && !isset($related['error'])}
                {foreach $related as $rp}
                    <article class="flex flex-col group">
                        <a href="{Url::post($rp->id)}" class="block">
                            <div class="aspect-[4/3] mb-6 overflow-hidden rounded-lg bg-surface-container-low relative">
                                {var $rp_img_raw = Posts::getPostImage($rp->id) ?: Themusk::getImage($rp->content)}
                                {var $rp_img = $rp_img_raw ? Url::thumb($rp_img_raw, 'large') : ''}
                                <img alt="Post thumbnail" class="w-full h-full object-cover grayscale transition-all duration-500 group-hover:grayscale-0 group-hover:scale-110" src="{if $rp_img}{$rp_img}{else}{Themusk::opt('default_image')}{/if}" />
                            </div>
                        </a>
                        <h3 class="font-headline text-xl font-bold text-on-surface mb-4 group-hover:text-primary transition-colors">
                            <a href="{Url::post($rp->id)}">{$rp->title}</a>
                        </h3>
                        <div class="mt-auto pt-4 text-outline text-[10px] font-label uppercase tracking-widest font-semibold">
                            {Date::format($rp->date, 'M d, Y')}
                        </div>
                    </article>
                {/foreach}
            {else}
                <p class="text-on-surface-variant italic">No related essays found.</p>
            {/if}
        </div>
    </div>

    <div class="post-content-max-w mx-auto pb-24 border-t border-outline-variant/10 pt-16 px-site">
        {if Comments::isEnable()}
        <div class="comments">
            {Comments::showList(['offset' => 0, 'max' => Options::v('comments_perpage'), 'parent' => 0])|noescape}
        </div>
        {Comments::form()|noescape}
        {/if}
    </div>
    {/foreach}

{else}
    <div class="text-center py-24">
        <h2 class="text-3xl font-headline font-bold mb-4">Post not found</h2>
        <a href="{Site::$url}" class="text-primary hover:underline font-label font-bold uppercase tracking-widest">Return Home</a>
    </div>
{/if}
