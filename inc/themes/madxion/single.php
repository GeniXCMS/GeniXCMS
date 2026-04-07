<!-- Post Hero Section -->
<section class="relative min-h-[70vh] flex items-center pt-32 pb-16 overflow-hidden bg-gradient-to-b from-surface-container to-background">
<div class="absolute inset-0 z-0 opacity-20 pointer-events-none" style="background-image: radial-gradient(#d20000 0.5px, transparent 0.5px); background-size: 40px 40px;"></div>
<div class="absolute top-1/4 -right-20 w-96 h-96 bg-primary-container/10 rounded-full blur-[120px]"></div>
<div class="max-w-[1440px] mx-auto px-8 w-full relative z-10">
{if isset($data['posts'][0]->title)}
<div>
<span class="inline-block label-md all-caps tracking-[0.2em] text-primary mb-6 font-label font-bold">02 // ARTICLE</span>
<h1 class="text-5xl md:text-7xl font-bold tracking-tighter leading-[1.1] mb-8 text-white">{$data['posts'][0]->title}</h1>
<div class="flex items-center gap-6 text-on-surface-variant">
<span class="text-sm font-label uppercase tracking-widest">{date('M d, Y', strtotime($data['posts'][0]->date))}</span>
<span class="w-2 h-2 bg-primary-container rounded-full"></span>
<span class="text-sm font-body">By {$data['posts'][0]->author ?? Site::$author ?? 'Admin'}</span>
</div>
</div>
{/if}
</section>

<!-- Post Content Section -->
<section class="py-24 bg-background">
<div class="max-w-[850px] mx-auto px-8">
{if isset($data['posts'][0]->title)}
{foreach $data['posts'] as $p}
<article class="bg-surface-container-low rounded-2xl p-12 md:p-16 border border-surface-container-high">
<!-- Featured Image -->
{if isset($p->image) && $p->image}
<div class="mb-12 rounded-xl overflow-hidden h-96 border border-surface-container-high">
<img src="{$p->image}" alt="{$p->title}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
</div>
{/if}

<!-- Post Meta -->
<div class="flex flex-wrap gap-4 mb-12 pb-8 border-b border-surface-container-high">
<span class="inline-block bg-primary-container/20 text-primary-container px-4 py-2 rounded-full text-xs font-bold uppercase tracking-wide">{if isset($p->category)}{$p->category}{else}Article{/if}</span>
</div>

<!-- Post Content -->
<div class="prose prose-invert max-w-none mb-12">
<div class="text-on-surface text-lg leading-relaxed space-y-6">
{Posts::content($p->content)|noescape}
</div>
</div>

<!-- Post Footer -->
<div class="border-t border-surface-container-high pt-8 mt-12">
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
<div>
<p class="text-sm text-on-surface-variant mb-2">Published on {date('M d, Y', strtotime($p->date))}</p>
<a href="{Site::$url}" class="inline-flex items-center gap-2 text-primary-container hover:text-primary transition-colors font-semibold">
<span class="material-symbols-outlined text-sm">arrow_back</span> Back to Home
</a>
</div>
</div>
</div>
</article>
{/foreach}
{else}
<div class="text-center py-20">
<p class="text-2xl text-on-surface-variant">Post not found.</p>
</div>
{/if}
</div>
</section>
