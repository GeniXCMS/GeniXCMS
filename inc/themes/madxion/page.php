<!-- Page Hero Section -->
<section class="relative min-h-[60vh] flex items-center pt-24 overflow-hidden bg-surface-container-low">
<div class="absolute inset-0 z-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(#d20000 0.5px, transparent 0.5px); background-size: 40px 40px;"></div>
<div class="max-w-[1440px] mx-auto px-8 w-full relative z-10">
{if isset($data['posts'][0]->title)}
<div>
<span class="inline-block label-md all-caps tracking-[0.2em] text-primary mb-4 font-label font-bold">Page</span>
<h1 class="text-5xl md:text-7xl font-bold tracking-tighter leading-[0.95] mb-6 text-white">{$data['posts'][0]->title}</h1>
<p class="text-xl text-on-surface-variant max-w-2xl font-body leading-relaxed">
{Posts::generateExcerpt($data['posts'][0]->content, 200)|noescape}
</p>
</div>
{/if}
</section>

<!-- Page Content Section -->
<section class="py-24 bg-background">
<div class="max-w-[1000px] mx-auto px-8">
<div class="bg-surface-container rounded-2xl p-12 border border-white/5">
{if isset($data['posts'][0]->title)}
{foreach $data['posts'] as $p}
<article class="prose prose-invert max-w-none">
<div class="text-on-surface leading-relaxed text-lg">
{Posts::content($p->content)|noescape}
</div>
</article>
{/foreach}
{else}
<div class="text-center py-20">
<p class="text-2xl text-on-surface-variant">Page not found.</p>
</div>
{/if}
</div>
</div>
</section>
