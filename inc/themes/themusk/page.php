
{if $posts}
    {foreach $posts as $p}
    <article class="max-w-4xl mx-auto py-12">
        <header class="mb-12 text-center">
            <h1 class="font-headline text-4xl md:text-6xl font-bold tracking-tighter text-on-surface mb-6 leading-tight">{$p->title}</h1>
        </header>

        {var $p_img = Themusk::getImage($p->content)}
        
        <div class="font-body text-xl prose-p:mb-6 prose-headings:font-headline prose-headings:font-bold prose-headings:mb-4 prose-headings:mt-8 prose-slate mx-auto text-on-surface-variant leading-relaxed">
            {Typo::Xclean($p->content)|noescape}
        </div>
    </article>
    {/foreach}

{else}
    <div class="text-center py-24">
        <h2 class="text-3xl font-headline font-bold mb-4">Page not found</h2>
        <a href="{Site::$url}" class="text-primary hover:underline font-label font-bold uppercase tracking-widest">Return Home</a>
    </div>
{/if}

