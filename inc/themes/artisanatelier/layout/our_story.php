<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * Artisan Atelier - Our Story Layout
 */
?>

<!-- Hero Section -->
<section class="relative min-h-[819px] flex items-center overflow-hidden py-24">
    <div class="absolute inset-0 bg-gradient-to-br from-primary-container via-surface to-surface z-0 opacity-40"></div>
    <div class="max-w-screen-2xl mx-auto px-12 grid grid-cols-12 gap-12 items-center relative z-10">
        <div class="col-span-12 lg:col-span-5">
            <span class="text-secondary font-semibold tracking-widest uppercase text-xs mb-4 block">Est. 2014</span>
            <h1 class="font-serif text-7xl font-bold leading-[1.1] text-on-surface mb-8 tracking-tight">Crafted with Soul &amp; Intention</h1>
            <p class="text-xl text-on-surface-variant leading-relaxed max-w-lg mb-10">
                At The Artisanal Atelier, we believe the objects we surround ourselves with should tell a story of human touch, patience, and the raw beauty of nature.
            </p>
        </div>
        <div class="col-span-12 lg:col-span-7 relative flex justify-end">
            <div class="relative w-4/5">
                <div class="absolute -top-10 -left-10 w-40 h-40 bg-tertiary-container rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
                <div class="tonal-layer">
                    <img alt="Our Founder" class="rounded-xl shadow-2xl object-cover aspect-[4/5] relative z-10" src="https://lh3.googleusercontent.com/aida-public/AB6AXuASKn_n-mVZXiYR7M0JmgM9glvIpNYeVEasSX7T8Sp6OSgo516OVsX5kJy5V-G9T63dmgcbgStP50PsLj4t_mQqjLEfYuvmcLQy0Iy1t7r0qThFjaRtZfsPlEiAYDg4DT5eXmtGr9ISgmrlVcStaEs_Xu9SaNE_tQtssxnQ0h-kOOu1O_eJqRl2ZcV_Xhg5OEGZxVkddU9D7Ue8UUbXDz6iRoLe1WMfMCT4SYaMB21Gg-B1TctmMaeo2FWJrLPP0vw4xRPoZVOe7QE"/>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Long-form Story Section -->
<section class="py-32 bg-surface">
    <div class="max-w-4xl mx-auto px-12">
        <h2 class="font-serif text-4xl text-secondary mb-16 text-center italic">The Journey of the Kiln</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-16">
            <div class="space-y-6">
                <p class="font-serif text-lg leading-relaxed text-on-surface first-letter:text-7xl first-letter:font-bold first-letter:text-primary first-letter:mr-3 first-letter:float-left">
                    Ten years ago, The Artisanal Atelier began in a small, drafty shed at the edge of a pine forest. What started as a personal quest for silence and tactile creation evolved into a sanctuary for traditional crafts that were being forgotten in the digital rush.
                </p>
            </div>
            <div class="space-y-6 md:mt-24">
                <p class="text-on-surface-variant leading-relaxed">
                    We didn't set out to build a brand. We set out to find the perfect weight of a linen sheet, the exact curvature of a mug that warms the hands, and the grain of paper that holds ink like a secret.
                </p>
                <blockquote class="border-l-4 border-primary-container pl-6 py-2">
                    <p class="font-serif italic text-2xl text-on-surface">"To craft is to meditate with your hands."</p>
                </blockquote>
            </div>
        </div>
    </div>
</section>

<!-- Handmade Process Section -->
<section class="py-32 bg-surface">
    <div class="max-w-screen-2xl mx-auto px-12">
        <div class="text-center mb-24">
            <h2 class="font-serif text-5xl text-on-surface mb-4">The Four Pillars</h2>
            <p class="text-on-surface-variant max-w-2xl mx-auto">How your chosen object comes to life, from the first spark of inspiration to the final touch.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
            <div class="text-center group">
                <div class="w-20 h-20 bg-surface-container-high rounded-full flex items-center justify-center mx-auto mb-8 group-hover:bg-primary-container transition-colors duration-500">
                    <span class="material-symbols-outlined text-primary scale-125">eco</span>
                </div>
                <h4 class="font-serif text-xl mb-4">Pure Sourcing</h4>
                <p class="text-sm text-on-surface-variant leading-relaxed">We select only organic, raw materials that respect the ecosystem from which they are taken.</p>
            </div>
            <div class="text-center group">
                <div class="w-20 h-20 bg-surface-container-high rounded-full flex items-center justify-center mx-auto mb-8 group-hover:bg-secondary-container transition-colors duration-500">
                    <span class="material-symbols-outlined text-secondary scale-125">draw</span>
                </div>
                <h4 class="font-serif text-xl mb-4">Intention Draft</h4>
                <p class="text-sm text-on-surface-variant leading-relaxed">Months are spent in the sketching phase, ensuring the ergonomics match the aesthetic vision.</p>
            </div>
            <div class="text-center group">
                <div class="w-20 h-20 bg-surface-container-high rounded-full flex items-center justify-center mx-auto mb-8 group-hover:bg-tertiary-container transition-colors duration-500">
                    <span class="material-symbols-outlined text-tertiary scale-125">precision_manufacturing</span>
                </div>
                <h4 class="font-serif text-xl mb-4">Slow Craft</h4>
                <p class="text-sm text-on-surface-variant leading-relaxed">No machines. No assembly lines. Just steady hands and focused patience over several days.</p>
            </div>
            <div class="text-center group">
                <div class="w-20 h-20 bg-surface-container-high rounded-full flex items-center justify-center mx-auto mb-8 group-hover:bg-primary-container/40 transition-colors duration-500">
                    <span class="material-symbols-outlined text-on-primary-fixed-variant scale-125">auto_awesome</span>
                </div>
                <h4 class="font-serif text-xl mb-4">Final Soul</h4>
                <p class="text-sm text-on-surface-variant leading-relaxed">Each item is hand-finished with a personal stamp, signifying it is ready for its new home.</p>
            </div>
        </div>
    </div>
</section>
