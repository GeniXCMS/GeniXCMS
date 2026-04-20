<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * Artisan Atelier - Contact Layout
 */
?>

<div class="max-w-7xl mx-auto px-8 py-12 md:py-20">
    <!-- Hero Section -->
    <header class="mb-20 max-w-3xl">
        <h1 class="text-6xl font-headline font-bold text-on-surface mb-6 leading-tight tracking-tight">
            Connect with the <span class="italic text-primary">Craft</span>
        </h1>
        <p class="text-xl font-body text-on-surface-variant leading-relaxed">
            Whether you have a question about a specific piece, wish to discuss a custom commission, or simply want to share a story, our studio doors are always open.
        </p>
    </header>

    <!-- Main Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
        <!-- Left Column -->
        <div class="lg:col-span-5 space-y-12">
            <div class="relative pt-12 pl-12">
                <div class="absolute top-0 left-0 w-64 h-80 bg-surface-container-highest rounded-xl z-0"></div>
                <img class="relative z-10 w-full h-[450px] object-cover rounded-lg shadow-sm" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDlrLZcc4Aq7mjFAbvc3RHX5xxtuNy7vrRj8HQbs78V_eO5XfwJX3pY_bBoGGY3O22cxG6rTx4jNIMeR7eaGNVBPmWkwj5UoaIG15HxJku0mt_YaW3v0HY2AfvqC8TMlr7cNeddhFjuii3IN1TKdQ-b3O9n0EC3B1jhZlSGgjaBFdkjZR9-A6Px6x9_fyzKRBvXjMEoCbsCI3EsSkeK0Q9XA_aJRnPYamp2JnRSMce24SJQefzERaE_o0M8bfrFVaXGCyBWNBO3MB8"/>
                <div class="absolute -bottom-8 -right-8 w-48 h-48 bg-primary-container rounded-full flex items-center justify-center p-8 text-center z-20 shadow-lg">
                    <span class="font-headline italic text-on-primary-container text-lg font-bold">Handcrafted in Heritage</span>
                </div>
            </div>

            <div class="bg-surface-container-low p-10 rounded-xl space-y-8">
                <div>
                    <h3 class="text-secondary font-headline text-2xl font-bold mb-4">Visit Our Studio</h3>
                    <p class="text-on-surface-variant font-body leading-loose">
                        <?=Options::v('site_address');?>
                    </p>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center gap-4 group">
                        <div class="w-10 h-10 rounded-full bg-secondary-container flex items-center justify-center text-secondary">
                            <span class="material-symbols-outlined text-xl">call</span>
                        </div>
                        <span class="text-on-surface font-medium"><?=Options::v('site_phone');?></span>
                    </div>
                    <div class="flex items-center gap-4 group">
                        <div class="w-10 h-10 rounded-full bg-secondary-container flex items-center justify-center text-secondary">
                            <span class="material-symbols-outlined text-xl">mail</span>
                        </div>
                        <span class="text-on-surface font-medium"><?=Options::v('site_email');?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Form -->
        <div class="lg:col-span-7 bg-surface-container-lowest border border-outline-variant/20 rounded-xl p-12 shadow-sm">
            <div class="mb-10">
                <h2 class="text-3xl font-headline font-bold text-on-surface mb-4">Send a Message</h2>
                <p class="text-on-surface-variant">Tell us what's on your mind. We typically respond within one business day.</p>
            </div>
            <form class="space-y-8" action="" method="POST">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="text-sm font-label font-semibold text-on-surface-variant uppercase tracking-wider">Your Name</label>
                        <input name="name" class="w-full bg-surface-container-low border-none focus:ring-2 focus:ring-primary rounded-xl p-4 text-on-surface outline-none transition-all" placeholder="Giotto di Bondone" type="text" required/>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-label font-semibold text-on-surface-variant uppercase tracking-wider">Email Address</label>
                        <input name="email" class="w-full bg-surface-container-low border-none focus:ring-2 focus:ring-primary rounded-xl p-4 text-on-surface outline-none transition-all" placeholder="giotto@renaissance.art" type="email" required/>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-label font-semibold text-on-surface-variant uppercase tracking-wider">Your Message</label>
                    <textarea name="message" class="w-full bg-surface-container-low border-none focus:ring-2 focus:ring-primary rounded-xl p-4 text-on-surface outline-none transition-all" placeholder="Describe the texture, the feeling, or the question you have..." rows="6" required></textarea>
                </div>
                <button class="w-full md:w-auto px-12 py-4 bg-primary text-on-primary font-bold text-lg rounded-full hover:bg-primary-dim active:scale-95 transition-all shadow-lg flex items-center justify-center gap-3" type="submit">
                    <span>Send Message</span>
                    <span class="material-symbols-outlined">send</span>
                </button>
            </form>
        </div>
    </div>
</div>
