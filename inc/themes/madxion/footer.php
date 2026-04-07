</main>
<!-- Footer -->
<footer class="w-full border-t border-white/5 bg-neutral-900 dark:bg-neutral-950">
<div class="grid grid-cols-1 md:grid-cols-4 gap-12 px-8 py-16 max-w-7xl mx-auto">
<div class="col-span-1 md:col-span-1">
<span class="text-xl font-black text-white font-headline mb-6 block uppercase">{Site::$name}</span>
<p class="text-neutral-500 text-sm font-body leading-relaxed">
{Site::$desc ?? 'Modern IT Solutions powered by GeniXCMS.'}
</p>
</div>
<div>
<h5 class="text-white font-bold uppercase text-[10px] tracking-[0.2em] mb-6 font-headline">Navigation</h5>
<ul class="space-y-4">
<li><a class="text-neutral-500 hover:text-orange-400 transition-colors text-sm font-body" href="{Site::$url}">Home</a></li>
<li><a class="text-neutral-500 hover:text-orange-400 transition-colors text-sm font-body" href="#">Services</a></li>
<li><a class="text-neutral-500 hover:text-orange-400 transition-colors text-sm font-body" href="#">About Us</a></li>
<li><a class="text-neutral-500 hover:text-orange-400 transition-colors text-sm font-body" href="#">Contact</a></li>
</ul>
</div>
<div>
<h5 class="text-white font-bold uppercase text-[10px] tracking-[0.2em] mb-6 font-headline">Legal</h5>
<ul class="space-y-4">
<li><a class="text-neutral-500 hover:text-orange-400 transition-colors text-sm font-body" href="#">Privacy Policy</a></li>
<li><a class="text-neutral-500 hover:text-orange-400 transition-colors text-sm font-body" href="#">Terms of Service</a></li>
<li><a class="text-neutral-500 hover:text-orange-400 transition-colors text-sm font-body" href="#">Security</a></li>
<li><a class="text-neutral-500 hover:text-orange-400 transition-colors text-sm font-body" href="#">Sitemap</a></li>
</ul>
</div>
<div>
<h5 class="text-white font-bold uppercase text-[10px] tracking-[0.2em] mb-6 font-headline">Connect</h5>
<div class="flex gap-4 mb-6">
{php $fb = MadxionTheme::get('social_fb'); $tw = MadxionTheme::get('social_tw'); $gh = MadxionTheme::get('social_gh');}
{if $fb}
<a class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center text-white hover:bg-primary-container transition-colors" href="{$fb}" target="_blank" rel="noopener">
<span class="material-symbols-outlined text-sm">facebook</span>
</a>
{/if}
{if $tw}
<a class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center text-white hover:bg-primary-container transition-colors" href="{$tw}" target="_blank" rel="noopener">
<span class="material-symbols-outlined text-sm">public</span>
</a>
{/if}
{if $gh}
<a class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center text-white hover:bg-primary-container transition-colors" href="{$gh}" target="_blank" rel="noopener">
<span class="material-symbols-outlined text-sm">code</span>
</a>
{/if}
</div>
<p class="text-neutral-500 text-xs font-body">© {date('Y')} {Site::$name}. All rights reserved.</p>
</div>
</div>
</footer>

<!-- Analytics & Ad Scripts -->
{php $analytics = MadxionTheme::get('mdo_analytics'); $adsense = MadxionTheme::get('mdo_adsense');}
{if $analytics}{$analytics|noescape}{/if}
{if $adsense}{$adsense|noescape}{/if}

{Site::footer()|noescape}
</body>
</html>
