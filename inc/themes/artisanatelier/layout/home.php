<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * Artisan Atelier - Home Layout
 */
?>

<!-- Hero Section -->
<section class="relative min-h-[870px] flex items-center overflow-hidden">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-r from-primary-container/40 to-transparent"></div>
        <img alt="Artisanal Pottery" class="w-full h-full object-cover object-center" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAnqrKkno9gZRdt0fYy2dnSelztird_BPZvV3NRPJTtKL5EvPeFix4qEOmxGKSCoQD9t6IpiNsi1Fz8Zrb9T05I3mfsvGAQ9Pa5d1nAQXJgosEYqdyWZQs1Diapxb-wnu4p-GTxV9B-jAPcU4UGhIfSivzLuQzKkAHqzElsKJdns1uHLjNoGOUc8A_GBa1yQvT67jwLjeaFZ3j9rknYPK3Ft21sjvVG2VcZpXkfMPXnBHTysEF9kx_lWVqJ2m_f-s1i_NWRGmA86Xo"/>
    </div>
    <div class="relative z-10 max-w-screen-2xl mx-auto px-12 w-full">
        <div class="max-w-2xl bg-white/20 backdrop-blur-md p-12 rounded-xl">
            <h1 class="text-7xl font-serif text-on-surface leading-tight tracking-tight mb-6">Objects of <br/><span class="italic text-primary">Quiet Intention</span></h1>
            <p class="text-xl text-on-surface-variant font-body mb-10 leading-relaxed max-w-lg">Curating a collection of handmade ceramics, linens, and art that celebrate the slow rhythm of the maker's hand.</p>
            <div class="flex gap-4">
                <button class="bg-primary text-on-primary px-10 py-5 rounded-xl font-semibold text-lg hover:shadow-xl transition-all hover:scale-[1.02]">Explore Collection</button>
                <button class="text-primary font-semibold px-10 py-5 border-b-2 border-primary-container hover:bg-primary-container/20 transition-all">Our Workshop</button>
            </div>
        </div>
    </div>
</section>

<!-- Welcome Section -->
<section class="py-24 bg-surface-container-low">
    <div class="max-w-screen-2xl mx-auto px-12 grid lg:grid-cols-2 gap-20 items-center">
        <div class="relative">
            <div class="absolute -top-10 -left-10 w-64 h-64 bg-tertiary-container/30 rounded-full blur-3xl"></div>
            <div class="blob-shape overflow-hidden shadow-2xl relative z-10">
                <img alt="Artisan at work" class="w-full h-[600px] object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCAkUEB5Glh0GVp7AMMsBZGSStIPTJHvZMUqzpAFUhFGXte-PYzkBt5SjN8CLZgdw7WpsjnerjneGWkU8od86VszN-2cT9iidVMehxIYXgj0W9s9a6RLGyw6PB_I83LEXD49QAD7WKAn3NGyEP3Yw0M-sf9KzMr6HdYxo0WditJ-I3mWTktn3WN4PqsuXrrjD0mxp1lsUc7wnFVrnhfcem80eoQWhDUdBndps8rlKfqavcL6J0Qhc29hciO11k793P-xfClbbcy2nA"/>
            </div>
        </div>
        <div class="space-y-8">
            <span class="text-secondary font-semibold tracking-widest uppercase text-sm">Our Philosophy</span>
            <h2 class="text-5xl font-serif text-on-surface leading-snug">Welcome to a space where <br/><em>craft meets soul.</em></h2>
            <p class="text-lg text-on-surface-variant leading-relaxed font-body">Every piece in The Artisanal Atelier is hand-selected for its unique character. We believe the objects you surround yourself with should tell a story—one of patience, natural materials, and the timeless beauty of imperfection.</p>
            <div class="pt-6">
                <a class="text-primary font-bold text-lg inline-flex items-center gap-2 group" href="#">
                    Meet the Artisans
                    <span class="material-symbols-outlined transition-transform group-hover:translate-x-2">arrow_right_alt</span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products (Dynamic Integration Ready) -->
<section class="py-32 bg-surface">
    <div class="max-w-screen-2xl mx-auto px-12">
        <div class="flex justify-between items-end mb-20">
            <div>
                <h2 class="text-headline-sm text-secondary mb-4 uppercase tracking-[0.2em]">Curated Favourites</h2>
                <h3 class="text-5xl font-serif text-on-surface">The Seasonal Edit</h3>
            </div>
            <a href="<?=Url::mod('nixomers');?>" class="text-on-surface-variant hover:text-primary transition-colors flex items-center gap-2">
                View All Products
                <span class="material-symbols-outlined">open_in_new</span>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
            <!-- Mock Product 1 -->
            <div class="group">
                <div class="bg-surface-container-low rounded-lg p-6 mb-6 transition-all group-hover:shadow-[0_32px_32px_-4px_rgba(57,56,50,0.06)]">
                    <img alt="Linen Tote" class="w-full aspect-[4/5] object-cover rounded-md mb-8" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCyaWIBj9TqRz5Ul5puJoIzlQRTZ6gyXw6TNugxkVY4Ktv17UWWQOJTVAXIIXMmX0ovzrp9cBVJAb_OfH7zVgjR97LEUf9pE0YOuIynKi9oPCyW5TsIEYuXrV0vNdfgq9780464EX186-ABFqUzFUR7mFMWSQ1zn1H2EUUNIhKArlc7DoIz3wRXRz4nOjVffquIExap6w_hW7N5rRdGWzdid7yI1oyaNcbj8Pg_cXb6IIXkBeDyahaVXip8IoOzgn1f3EAqJ1ZvusU"/>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-stone-400 text-sm mb-1 uppercase tracking-tighter">Linens</p>
                            <h4 class="text-2xl font-serif text-on-surface group-hover:text-primary transition-colors">The Weaver's Tote</h4>
                        </div>
                        <span class="text-xl font-serif text-on-surface">$85</span>
                    </div>
                </div>
            </div>
            <!-- Mock Product 2 -->
            <div class="group mt-12">
                <div class="bg-surface-container-low rounded-lg p-6 mb-6 transition-all group-hover:shadow-[0_32px_32px_-4px_rgba(57,56,50,0.06)]">
                    <img alt="Mini Painting" class="w-full aspect-[4/5] object-cover rounded-md mb-8" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAm4V0QqDXi5_3vYyAEjtUD07s3phxKZwgu8R4M4Z86RH4CIiT62CO9dJ9tZHWnOIiztfCLhblwvL7d7eFFM6IwvWOe0UVDvsUtAUFqs6mXnmlR3Id_ol9xr_LuLCTDchVJqvrBKtykDU0hljqqvua_lCrNQ0bzlkPND15RoJwivYIrB4BbnSIQ5MReu4a3CyP3M3Ducnkj0Mjz_FH9BgZq8Aa9oO27GYa8jq7vHKJcyUA4UUs19CmvSybtZhs9vANpjA7dDEjJgBA"/>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-stone-400 text-sm mb-1 uppercase tracking-tighter">Art Prints</p>
                            <h4 class="text-2xl font-serif text-on-surface group-hover:text-primary transition-colors">Morning Mist Study</h4>
                        </div>
                        <span class="text-xl font-serif text-on-surface">$120</span>
                    </div>
                </div>
            </div>
            <!-- Mock Product 3 -->
            <div class="group">
                <div class="bg-surface-container-low rounded-lg p-6 mb-6 transition-all group-hover:shadow-[0_32px_32px_-4px_rgba(57,56,50,0.06)]">
                    <img alt="Ceramic Bowl" class="w-full aspect-[4/5] object-cover rounded-md mb-8" src="https://lh3.googleusercontent.com/aida-public/AB6AXuD03LCkB722EyRzdT6hAvsvvhg8uHbrNO5r2uH_0pekV--IF4odaQwiLTCEOsZwGncNSRKYVKvKru2z4sBLJ2TX1kN_VQ00numihApsZxexQ_h7p9aHpAnIu1JUqbsH5jyqxp6vK7sF4VFdPWfitQt3mruY8g9_dsZGw_ZGodyBx8hukBkhKQ7ZhNNjtWhyzQkNVGlLohT1NdO3vxdqgOeumWyFJb_cxb3jusREQ1JuhZ-erXbEcKZbFWKMnkBWbnJTNpis5PBqNMs"/>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-stone-400 text-sm mb-1 uppercase tracking-tighter">Ceramics</p>
                            <h4 class="text-2xl font-serif text-on-surface group-hover:text-primary transition-colors">Sanded Earth Bowl</h4>
                        </div>
                        <span class="text-xl font-serif text-on-surface">$64</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Follow the Process -->
<section class="py-32 bg-white">
    <div class="max-w-screen-2xl mx-auto px-12">
        <div class="grid lg:grid-cols-3 gap-16">
            <div class="lg:sticky lg:top-32 h-fit">
                <h2 class="text-5xl font-serif leading-tight mb-8">Follow the <br/><span class="italic">Process</span></h2>
                <p class="text-on-surface-variant text-lg leading-relaxed mb-8">Transparency is the core of our craft. Witness the journey from raw clay to the finished masterpiece through our digital journal.</p>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full border border-outline-variant flex items-center justify-center">
                        <span class="material-symbols-outlined text-stone-400">play_arrow</span>
                    </div>
                    <span class="font-bold tracking-widest text-xs uppercase">Watch The Film</span>
                </div>
            </div>
            <div class="lg:col-span-2 grid grid-cols-2 gap-8">
                <div class="space-y-8 mt-12">
                    <div class="overflow-hidden rounded-lg">
                        <img alt="Kiln firing" class="w-full aspect-square object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCrrswF8-N8vT-BHoAOgsOxeiEq5_xyTkAGQTX6fan86vGscNUq0tmWojXGNdTTyWVsj7TOLuEDIUStgwPGB66T0qTzkaFis9wDv0dYz9mQDcPWsaf_VaIpG2zVRW_sI2kmWAE7Dy8HrNCoCdWA3w2gpMVbPzLjd9FVuHTBrva9n_KZGftw_gsSXWL7AJaXJfyRGAHTKDU_mXLXuHldnojf9lAtQ5J1iNkl146yBix75gwlEVJuqCRKeS94OCzvogOBsyVrZwqCQzA"/>
                    </div>
                    <div class="p-4 border-l-2 border-primary-container">
                        <h5 class="font-serif text-xl mb-2">01. The Firing Stage</h5>
                        <p class="text-on-surface-variant text-sm">Where the earth transforms. Temperatures reaching 1200°C solidify the soul of the piece.</p>
                    </div>
                </div>
                <div class="space-y-8">
                    <div class="overflow-hidden rounded-lg">
                        <img alt="Glazing process" class="w-full aspect-square object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDOmMptxEf2CzRPxhiRyqYgTxOm56Rw8WT_FcMC_ful29xmgZpx0O3SnwA6aS_mjK1yhQC7T9Yc_SCtvWPQZKhnQwYwxbRgE-wtzlCaWeA2Nop4jPFiz3YkdPPNOo3fT8k0IIAjf7tznd9hrOq-9n4sx3s0duwBZLXzTYmyVXdWMF3TdW6e_R7_x1oIlhuv64jud959seke9qrsqp43GvtYXnx4xvQaRYIFHb2lnQNrkrs7UJt9dYkJu2_iZdO1RXfr179Gg_BukLs"/>
                    </div>
                    <div class="p-4 border-l-2 border-secondary-container">
                        <h5 class="font-serif text-xl mb-2">02. Hand-Applied Glaze</h5>
                        <p class="text-on-surface-variant text-sm">No two drips are the same. Our custom-mixed glazes respond to the heat in unpredictable, beautiful ways.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="py-24 px-12">
    <div class="max-w-4xl mx-auto bg-primary-container/20 rounded-[2rem] p-16 text-center relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/30 rounded-full blur-2xl"></div>
        <div class="absolute -bottom-10 -left-10 w-60 h-60 bg-primary/5 rounded-full blur-2xl"></div>
        <h2 class="text-4xl font-serif mb-6 relative z-10">Join our Inner Circle</h2>
        <p class="text-on-surface-variant mb-10 text-lg relative z-10">Receive early access to new kiln openings, artisan stories, and exclusive workshop invitations directly in your inbox.</p>
        <form class="flex flex-col md:flex-row gap-4 relative z-10 max-w-lg mx-auto">
            <input class="flex-grow bg-surface-container-lowest border-none px-6 py-4 rounded-xl focus:ring-2 focus:ring-primary shadow-sm text-on-surface" placeholder="Your email address" type="email"/>
            <button class="bg-primary text-on-primary px-8 py-4 rounded-xl font-semibold hover:bg-primary-dim transition-colors" type="submit">Subscribe</button>
        </form>
    </div>
</section>
