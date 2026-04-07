{* Madxion Theme Index - Latte Template *}
{var $heroBg = MadxionTheme::get('hero_bg_image')}
{var $heroBgEnable = MadxionTheme::get('hero_bg_enable') === 'on'}
{var $showPattern = MadxionTheme::get('show_hero_pattern') === 'on'}
{var $enableBlur = MadxionTheme::get('enable_blur_effect') === 'on'}
{var $enableAnimations = MadxionTheme::get('enable_animations') === 'on'}
{var $heroClass = $enableAnimations ? 'animate-fade-in' : ''}
{var $heroLabel = MadxionTheme::get('hero_section_label', '01 // THE DIGITAL FRONTIER')}
{var $heroHeadline = MadxionTheme::get('hero_headline', 'Revolutionizing IT Solutions')}
{var $heroHeadlineAccent = MadxionTheme::get('hero_headline_accent', 'for the Digital Age.')}
{var $heroDescription = MadxionTheme::get('hero_description', 'We architect elite digital infrastructures that transform complex challenges into competitive advantages. Powering the next generation of global enterprises.')}
{var $heroCtaPrimaryLabel = MadxionTheme::get('hero_cta_primary_label', 'Get Started')}
{var $heroCtaPrimaryUrl = MadxionTheme::get('hero_cta_primary_url', '#contact')}
{var $heroCtaSecondaryLabel = MadxionTheme::get('hero_cta_secondary_label', 'View Ecosystem')}
{var $heroCtaSecondaryUrl = MadxionTheme::get('hero_cta_secondary_url', '#ecosystem')}
{var $heroImage = MadxionTheme::get('hero_section_image')}
{var $expertiseLabel = MadxionTheme::get('expertise_label', '02 // CORE DOMAINS')}
{var $expertiseTitle = MadxionTheme::get('expertise_title', 'Our Expertise')}
{var $expertiseDescription = MadxionTheme::get('expertise_description', 'Deep technical mastery across the full stack of modern enterprise infrastructure.')}
{var $expertiseCard1Icon = MadxionTheme::get('expertise_card_1_icon', 'terminal')}
{var $expertiseCard1Title = MadxionTheme::get('expertise_card_1_title', 'Intelligent Software')}
{var $expertiseCard1Text = MadxionTheme::get('expertise_card_1_text', 'Custom-built applications designed for high performance, scalability, and seamless user experiences across platforms.')}
{var $expertiseCard1Image = MadxionTheme::get('expertise_card_1_image')}
{var $expertiseCard2Icon = MadxionTheme::get('expertise_card_2_icon', 'cloud_done')}
{var $expertiseCard2Title = MadxionTheme::get('expertise_card_2_title', 'Cloud Infrastructure')}
{var $expertiseCard2Text = MadxionTheme::get('expertise_card_2_text', 'Cloud-native solutions that provide elastic scalability and global distribution for massive datasets.')}
{var $expertiseCard3Icon = MadxionTheme::get('expertise_card_3_icon', 'shield_lock')}
{var $expertiseCard3Title = MadxionTheme::get('expertise_card_3_title', 'Cyber Sentinel')}
{var $expertiseCard3Text = MadxionTheme::get('expertise_card_3_text', 'Military-grade encryption and proactive threat hunting to protect your organization\'s most vital assets.')}
{var $expertiseCard4Icon = MadxionTheme::get('expertise_card_4_icon', 'insights')}
{var $expertiseCard4Title = MadxionTheme::get('expertise_card_4_title', 'Predictive Analytics')}
{var $expertiseCard4Text = MadxionTheme::get('expertise_card_4_text', 'Turning raw data into actionable intelligence through advanced AI modeling and real-time processing.')}
{var $expertiseCard4Image = MadxionTheme::get('expertise_card_4_image')}
{var $expertiseCardBorderOpacity = MadxionTheme::get('expertise_card_border_opacity', '5')}
{var $advantageLabel = MadxionTheme::get('advantage_label', '03 // THE MADXION ADVANTAGE')}
{var $advantageTitle = MadxionTheme::get('advantage_title', 'Why Madxion?')}
{var $advantageItem1Title = MadxionTheme::get('advantage_item_1_title', 'The Kinetic Monolith Architecture')}
{var $advantageItem1Text = MadxionTheme::get('advantage_item_1_text', 'Our proprietary design framework ensures every system is stable as a monolith but fluid as kinetic energy.')}
{var $advantageItem2Title = MadxionTheme::get('advantage_item_2_title', 'Radical Transparency')}
{var $advantageItem2Text = MadxionTheme::get('advantage_item_2_text', 'Full visibility into every line of code, every cloud node, and every security protocol we implement.')}
{var $advantageItem3Title = MadxionTheme::get('advantage_item_3_title', 'Human-Centric Reliability')}
{var $advantageItem3Text = MadxionTheme::get('advantage_item_3_text', 'Technology is built by humans for humans. We prioritize intuitive operation for complex systems.')}
{var $validationLabel = MadxionTheme::get('validation_label', '04 // VALIDATION')}
{var $validationTitle = MadxionTheme::get('validation_title', 'Success Stories')}
{var $validationDescription = MadxionTheme::get('validation_description', 'Real results from real clients.')}
{var $validationTestimonial1Stat = MadxionTheme::get('validation_testimonial_1_stat', '300%')}
{var $validationTestimonial1StatLabel = MadxionTheme::get('validation_testimonial_1_stat_label', 'Scale Acceleration')}
{var $validationTestimonial1Quote = MadxionTheme::get('validation_testimonial_1_quote', 'Madxion didn\'t just fix our IT; they rebuilt our digital DNA. We scaled from regional to global in 18 months.')}
{var $validationTestimonial1Name = MadxionTheme::get('validation_testimonial_1_name', 'Jameson Dovrak')}
{var $validationTestimonial1Position = MadxionTheme::get('validation_testimonial_1_position', 'CTO, Nexus Dynamics')}
{var $validationTestimonial1Initials = MadxionTheme::get('validation_testimonial_1_initials', 'JD')}
{var $validationTestimonial1Color = MadxionTheme::get('validation_testimonial_1_color', 'primary')}
{var $validationTestimonial2Stat = MadxionTheme::get('validation_testimonial_2_stat', 'Zero')}
{var $validationTestimonial2StatLabel = MadxionTheme::get('validation_testimonial_2_stat_label', 'Security Breaches')}
{var $validationTestimonial2Quote = MadxionTheme::get('validation_testimonial_2_quote', 'Their security protocols are unmatched. We feel fortified for the first time in our company\'s history.')}
{var $validationTestimonial2Name = MadxionTheme::get('validation_testimonial_2_name', 'Elena Laurent')}
{var $validationTestimonial2Position = MadxionTheme::get('validation_testimonial_2_position', 'Head of Security, FinSafe')}
{var $validationTestimonial2Initials = MadxionTheme::get('validation_testimonial_2_initials', 'EL')}
{var $validationTestimonial2Color = MadxionTheme::get('validation_testimonial_2_color', 'secondary')}
{var $validationTestimonial3Stat = MadxionTheme::get('validation_testimonial_3_stat', '40ms')}
{var $validationTestimonial3StatLabel = MadxionTheme::get('validation_testimonial_3_stat_label', 'Global Latency')}
{var $validationTestimonial3Quote = MadxionTheme::get('validation_testimonial_3_quote', 'The edge computing solution provided by Madxion revolutionized our user retention rates overnight.')}
{var $validationTestimonial3Name = MadxionTheme::get('validation_testimonial_3_name', 'Marcus Kane')}
{var $validationTestimonial3Position = MadxionTheme::get('validation_testimonial_3_position', 'CEO, StreamVault')}
{var $validationTestimonial3Initials = MadxionTheme::get('validation_testimonial_3_initials', 'MK')}
{var $validationTestimonial3Color = MadxionTheme::get('validation_testimonial_3_color', 'white')}
{var $validationCardBorderOpacity = MadxionTheme::get('validation_card_border_opacity', '5')}
{var $postsSectionTitle = MadxionTheme::get('posts_section_title', 'Latest Insights')}
{var $postsSectionSubtitle = MadxionTheme::get('posts_section_subtitle', 'Stay updated with our latest thoughts and industry insights.')}
{var $postsLimit = (int) MadxionTheme::get('posts_limit', 6)}
{var $postsCardBorderOpacity = MadxionTheme::get('posts_card_border_opacity', '20')}
{var $expertiseCard1BorderColor = MadxionTheme::get('expertise_card_1_border_color', 'white')}
{var $expertiseCard1BorderOpacity = MadxionTheme::get('expertise_card_1_border_opacity', '5')}
{var $expertiseCard1BorderHoverOpacity = MadxionTheme::get('expertise_card_1_border_hover_opacity', '30')}
{var $expertiseCard1BgColor = MadxionTheme::get('expertise_card_1_bg_color', 'surface')}
{var $expertiseCard2BorderColor = MadxionTheme::get('expertise_card_2_border_color', 'white')}
{var $expertiseCard2BorderOpacity = MadxionTheme::get('expertise_card_2_border_opacity', '5')}
{var $expertiseCard2BorderHoverOpacity = MadxionTheme::get('expertise_card_2_border_hover_opacity', '0')}
{var $expertiseCard2BgColor = MadxionTheme::get('expertise_card_2_bg_color', 'surface-container-high')}
{var $expertiseCard3BorderColor = MadxionTheme::get('expertise_card_3_border_color', 'white')}
{var $expertiseCard3BorderOpacity = MadxionTheme::get('expertise_card_3_border_opacity', '5')}
{var $expertiseCard3BorderHoverOpacity = MadxionTheme::get('expertise_card_3_border_hover_opacity', '0')}
{var $expertiseCard3BgColor = MadxionTheme::get('expertise_card_3_bg_color', 'surface-container-high')}
{var $expertiseCard4BorderColor = MadxionTheme::get('expertise_card_4_border_color', 'white')}
{var $expertiseCard4BorderOpacity = MadxionTheme::get('expertise_card_4_border_opacity', '5')}
{var $expertiseCard4BorderHoverOpacity = MadxionTheme::get('expertise_card_4_border_hover_opacity', '30')}
{var $expertiseCard4BgColor = MadxionTheme::get('expertise_card_4_bg_color', 'surface')}
{var $validationCard1BorderColor = MadxionTheme::get('validation_card_1_border_color', 'white')}
{var $validationCard1BorderOpacity = MadxionTheme::get('validation_card_1_border_opacity', '5')}
{var $validationCard1BorderHoverOpacity = MadxionTheme::get('validation_card_1_border_hover_opacity', '0')}
{var $validationCard1BgColor = MadxionTheme::get('validation_card_1_bg_color', 'surface-container-low')}
{var $validationCard2BorderColor = MadxionTheme::get('validation_card_2_border_color', 'white')}
{var $validationCard2BorderOpacity = MadxionTheme::get('validation_card_2_border_opacity', '5')}
{var $validationCard2BorderHoverOpacity = MadxionTheme::get('validation_card_2_border_hover_opacity', '0')}
{var $validationCard2BgColor = MadxionTheme::get('validation_card_2_bg_color', 'surface-container-low')}
{var $validationCard3BorderColor = MadxionTheme::get('validation_card_3_border_color', 'white')}
{var $validationCard3BorderOpacity = MadxionTheme::get('validation_card_3_border_opacity', '5')}
{var $validationCard3BorderHoverOpacity = MadxionTheme::get('validation_card_3_border_hover_opacity', '0')}
{var $validationCard3BgColor = MadxionTheme::get('validation_card_3_bg_color', 'surface-container-low')}
{var $postsCardBorderColor = MadxionTheme::get('posts_card_border_color', 'primary-container')}
{var $postsCardBorderHoverOpacity = MadxionTheme::get('posts_card_border_hover_opacity', '100')}
{var $postsCardBgColor = MadxionTheme::get('posts_card_bg_color', 'surface-container-low')}
{var $heroImageBorderEnable = MadxionTheme::get('hero_image_border_enable', 'off')}
{var $heroImageBorderColor = MadxionTheme::get('hero_image_border_color', 'white')}
{var $heroImageBorderOpacity = MadxionTheme::get('hero_image_border_opacity', '5')}
<section class="relative min-h-screen flex items-start pt-16 overflow-hidden {$heroClass}" {if $heroBg && $heroBgEnable}style="background-image: url({$heroBg}); background-size: cover; background-position: center;"{/if}>
<!-- Background Network Pattern -->
{if $showPattern}
<div class="absolute inset-0 z-0 opacity-20 pointer-events-none" style="background-image: radial-gradient(#d20000 0.5px, transparent 0.5px); background-size: 40px 40px;"></div>
{/if}
{if $enableBlur}
<div class="absolute inset-0 z-0 opacity-30 pointer-events-none blur-3xl" style="background: radial-gradient(circle at top left, rgba(255, 180, 168, 0.18), transparent 37%);"></div>
{/if}
<div class="absolute top-1/4 -right-20 w-96 h-96 bg-primary-container/20 rounded-full blur-[120px]"></div>
<div class="absolute bottom-1/4 -left-20 w-80 h-80 bg-secondary-container/10 rounded-full blur-[100px]"></div>
<div class="max-w-[1440px] mx-auto px-8 w-full relative z-10 grid lg:grid-cols-2 gap-16 items-center">
<div>
<span class="inline-block label-md all-caps tracking-[0.2em] text-primary mb-6 font-label font-bold">{$heroLabel|noescape}</span>
<h1 class="text-6xl md:text-8xl font-bold tracking-tighter leading-[0.95] mb-8 text-white">
{$heroHeadline|noescape} <br/>
<span class="text-primary-container">{$heroHeadlineAccent|noescape}</span>
</h1>
<p class="text-xl text-on-surface-variant max-w-lg mb-10 font-body leading-relaxed">
{$heroDescription|noescape}
</p>
<div class="flex flex-wrap gap-4">
<a href="{$heroCtaPrimaryUrl|noescape}" class="kinetic-gradient px-8 py-4 rounded-xl text-white font-extrabold text-lg flex items-center gap-3 transition-all hover:brightness-110">{$heroCtaPrimaryLabel|noescape}<span class="material-symbols-outlined">arrow_forward</span></a>
<a href="{$heroCtaSecondaryUrl|noescape}" class="px-8 py-4 rounded-xl border border-outline-variant/30 text-secondary font-bold text-lg hover:bg-surface-container-high transition-all">{$heroCtaSecondaryLabel|noescape}</a>
</div>
</div>
<div class="relative">
<div class="aspect-square rounded-3xl overflow-hidden shadow-2xl{if $heroImageBorderEnable === 'on'} border border-{$heroImageBorderColor}/{$heroImageBorderOpacity}{/if} bg-surface-container-low">
{if $heroImage}
<img class="w-full h-full object-cover" src="{$heroImage|noescape}" alt="{$heroHeadline|noescape}">
{else}
<div class="w-full h-full bg-surface-container-high"></div>
{/if}
</div>
</div>
</div>
</section>

<!-- Our Expertise Section -->
<section class="py-32 bg-surface-container-low">
<div class="max-w-[1440px] mx-auto px-8">
<div class="flex flex-col md:flex-row md:items-end justify-between mb-20 gap-8">
<div class="max-w-2xl">
<span class="label-md all-caps tracking-[0.2em] text-primary mb-4 block font-label font-bold">{$expertiseLabel|noescape}</span>
<h2 class="text-5xl md:text-6xl font-bold tracking-tighter text-white">{$expertiseTitle|noescape}</h2>
</div>
<p class="text-on-surface-variant max-w-sm mb-2">{$expertiseDescription|noescape}</p>
</div>
<div class="grid grid-cols-1 md:grid-cols-12 gap-6">
<div class="md:col-span-8 group bg-{$expertiseCard1BgColor} rounded-3xl p-10 overflow-hidden relative border border-{$expertiseCard1BorderColor}/{$expertiseCard1BorderOpacity} expertise-card-1 transition-all duration-500">
<div class="relative z-10">
<span class="material-symbols-outlined text-primary-container text-5xl mb-6" style="font-variation-settings: 'FILL' 1;">{$expertiseCard1Icon|noescape}</span>
<h3 class="text-3xl font-bold text-white mb-4">{$expertiseCard1Title|noescape}</h3>
<p class="text-on-surface-variant max-w-md text-lg">{$expertiseCard1Text|noescape}</p>
</div>
{if $expertiseCard1Image}
<img class="absolute right-0 top-0 w-1/2 h-full object-cover opacity-20 grayscale group-hover:grayscale-0 group-hover:opacity-40 transition-all duration-700" src="{$expertiseCard1Image|noescape}" alt="{$expertiseCard1Title|noescape}">
{/if}
</div>
<div class="md:col-span-4 bg-{$expertiseCard2BgColor} rounded-3xl p-10 border border-{$expertiseCard2BorderColor}/{$expertiseCard2BorderOpacity} expertise-card-2 transition-colors">
<span class="material-symbols-outlined text-secondary text-5xl mb-6">{$expertiseCard2Icon|noescape}</span>
<h3 class="text-3xl font-bold text-white mb-4">{$expertiseCard2Title|noescape}</h3>
<p class="text-on-surface-variant">{$expertiseCard2Text|noescape}</p>
</div>
<div class="md:col-span-4 bg-{$expertiseCard3BgColor} rounded-3xl p-10 border border-{$expertiseCard3BorderColor}/{$expertiseCard3BorderOpacity} expertise-card-3 transition-colors">
<span class="material-symbols-outlined text-primary text-5xl mb-6">{$expertiseCard3Icon|noescape}</span>
<h3 class="text-3xl font-bold text-white mb-4">{$expertiseCard3Title|noescape}</h3>
<p class="text-on-surface-variant">{$expertiseCard3Text|noescape}</p>
</div>
<div class="md:col-span-8 group bg-{$expertiseCard4BgColor} rounded-3xl p-10 overflow-hidden relative border border-{$expertiseCard4BorderColor}/{$expertiseCard4BorderOpacity} expertise-card-4 transition-all duration-500">
<div class="relative z-10 flex flex-col justify-center h-full">
<span class="material-symbols-outlined text-secondary-container text-5xl mb-6">{$expertiseCard4Icon|noescape}</span>
<h3 class="text-3xl font-bold text-white mb-4">{$expertiseCard4Title|noescape}</h3>
<p class="text-on-surface-variant max-w-md text-lg">{$expertiseCard4Text|noescape}</p>
</div>
{if $expertiseCard4Image}
<img class="absolute right-0 bottom-0 w-1/2 h-full object-cover opacity-10 group-hover:opacity-30 transition-all duration-700" src="{$expertiseCard4Image|noescape}" alt="{$expertiseCard4Title|noescape}">
{/if}
</div>
</div>
</div>
</section>

<!-- Why Madxion Section -->
<section class="py-32 relative overflow-hidden bg-surface-container-low">
<div class="max-w-[1440px] mx-auto px-8 grid lg:grid-cols-2 gap-24 items-center">
<div class="order-2 lg:order-1">
<div class="grid grid-cols-2 gap-8">
<div class="space-y-4">
<div class="h-64 rounded-3xl overflow-hidden">
<div class="w-full h-full bg-surface-container-high"></div>
</div>
<div class="bg-surface-container-low p-8 rounded-3xl">
<h4 class="text-white font-bold text-2xl mb-2">{$advantageItem1Title|noescape}</h4>
<p class="text-on-surface-variant text-sm">{$advantageItem1Text|noescape}</p>
</div>
</div>
<div class="space-y-4 mt-12">
<div class="bg-primary-container p-8 rounded-3xl text-on-primary-container">
<h4 class="font-bold text-2xl mb-2">{$advantageItem2Title|noescape}</h4>
<p class="text-on-primary-container/80 text-sm">{$advantageItem2Text|noescape}</p>
</div>
<div class="h-64 rounded-3xl overflow-hidden">
<div class="w-full h-full bg-surface-container-high"></div>
</div>
</div>
</div>
</div>
<div class="order-1 lg:order-2">
<span class="label-md all-caps tracking-[0.2em] text-secondary mb-6 block font-label font-bold">{$advantageLabel|noescape}</span>
<h2 class="text-5xl md:text-7xl font-bold tracking-tighter text-white mb-8">{$advantageTitle|noescape}</h2>
<div class="space-y-8">
<div class="flex gap-6 items-start">
<span class="text-primary-container font-headline text-3xl font-bold">01.</span>
<div>
<h4 class="text-xl font-bold text-white mb-2">{$advantageItem1Title|noescape}</h4>
<p class="text-on-surface-variant leading-relaxed">{$advantageItem1Text|noescape}</p>
</div>
</div>
<div class="flex gap-6 items-start">
<span class="text-primary-container font-headline text-3xl font-bold">02.</span>
<div>
<h4 class="text-xl font-bold text-white mb-2">{$advantageItem2Title|noescape}</h4>
<p class="text-on-surface-variant leading-relaxed">{$advantageItem2Text|noescape}</p>
</div>
</div>
<div class="flex gap-6 items-start">
<span class="text-primary-container font-headline text-3xl font-bold">03.</span>
<div>
<h4 class="text-xl font-bold text-white mb-2">{$advantageItem3Title|noescape}</h4>
<p class="text-on-surface-variant leading-relaxed">{$advantageItem3Text|noescape}</p>
</div>
</div>
</div>
</div>
</div>
</section>

<!-- Validation Section -->
<section class="py-32 bg-surface-container-low">
<div class="max-w-[1440px] mx-auto px-8">
<div class="text-center mb-20">
<span class="label-md all-caps tracking-[0.2em] text-primary mb-4 block font-label font-bold">{$validationLabel|noescape}</span>
<h2 class="text-5xl md:text-6xl font-bold tracking-tighter text-white">{$validationTitle|noescape}</h2>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
<div class="bg-{$validationCard1BgColor} rounded-3xl p-10 border border-{$validationCard1BorderColor}/{$validationCard1BorderOpacity} validation-card-1 relative group">
<div class="absolute top-10 right-10 opacity-10 group-hover:opacity-40 transition-opacity">
<span class="material-symbols-outlined text-6xl">format_quote</span>
</div>
<div class="mb-12">
<h4 class="text-primary-container font-black text-4xl mb-2">{$validationTestimonial1Stat|noescape}</h4>
<p class="text-on-surface-variant text-sm uppercase tracking-widest font-label">{$validationTestimonial1StatLabel|noescape}</p>
</div>
<p class="text-on-surface text-lg italic mb-10 leading-relaxed">"{$validationTestimonial1Quote|noescape}"</p>
<div class="flex items-center gap-4">
<div class="w-12 h-12 rounded-full bg-surface-container-highest flex items-center justify-center font-bold text-{$validationTestimonial1Color}">{$validationTestimonial1Initials|noescape}</div>
<div>
<p class="text-white font-bold">{$validationTestimonial1Name|noescape}</p>
<p class="text-on-surface-variant text-xs">{$validationTestimonial1Position|noescape}</p>
</div>
</div>
</div>
<div class="bg-{$validationCard2BgColor} rounded-3xl p-10 border border-{$validationCard2BorderColor}/{$validationCard2BorderOpacity} validation-card-2 relative group">
<div class="absolute top-10 right-10 opacity-10 group-hover:opacity-40 transition-opacity">
<span class="material-symbols-outlined text-6xl">format_quote</span>
</div>
<div class="mb-12">
<h4 class="text-secondary font-black text-4xl mb-2">{$validationTestimonial2Stat|noescape}</h4>
<p class="text-on-surface-variant text-sm uppercase tracking-widest font-label">{$validationTestimonial2StatLabel|noescape}</p>
</div>
<p class="text-on-surface text-lg italic mb-10 leading-relaxed">"{$validationTestimonial2Quote|noescape}"</p>
<div class="flex items-center gap-4">
<div class="w-12 h-12 rounded-full bg-surface-container-highest flex items-center justify-center font-bold text-{$validationTestimonial2Color}">{$validationTestimonial2Initials|noescape}</div>
<div>
<p class="text-white font-bold">{$validationTestimonial2Name|noescape}</p>
<p class="text-on-surface-variant text-xs">{$validationTestimonial2Position|noescape}</p>
</div>
</div>
</div>
<div class="bg-{$validationCard3BgColor} rounded-3xl p-10 border border-{$validationCard3BorderColor}/{$validationCard3BorderOpacity} validation-card-3 relative group">
<div class="absolute top-10 right-10 opacity-10 group-hover:opacity-40 transition-opacity">
<span class="material-symbols-outlined text-6xl">format_quote</span>
</div>
<div class="mb-12">
<h4 class="text-white font-black text-4xl mb-2">{$validationTestimonial3Stat|noescape}</h4>
<p class="text-on-surface-variant text-sm uppercase tracking-widest font-label">{$validationTestimonial3StatLabel|noescape}</p>
</div>
<p class="text-on-surface text-lg italic mb-10 leading-relaxed">"{$validationTestimonial3Quote|noescape}"</p>
<div class="flex items-center gap-4">
<div class="w-12 h-12 rounded-full bg-surface-container-highest flex items-center justify-center font-bold text-{$validationTestimonial3Color}">{$validationTestimonial3Initials|noescape}</div>
<div>
<p class="text-white font-bold">{$validationTestimonial3Name|noescape}</p>
<p class="text-on-surface-variant text-xs">{$validationTestimonial3Position|noescape}</p>
</div>
</div>
</div>
</div>
</div>
</section>

<!-- Posts Grid Section -->
<section class="py-32 bg-surface-container-low">
<div class="max-w-[1440px] mx-auto px-8">
<div class="text-center mb-20">
<h2 class="text-5xl md:text-6xl font-bold tracking-tighter text-white mb-6">{$postsSectionTitle|noescape}</h2>
<p class="text-on-surface-variant text-xl max-w-2xl mx-auto">{$postsSectionSubtitle|noescape}</p>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
{if isset($data['num']) && $data['num'] > 0}
{foreach array_slice($data['posts'], 0, $postsLimit) as $p}
<!-- Post Card -->
<article class="group relative bg-{$postsCardBgColor} p-10 rounded-3xl transition-all duration-500 hover:bg-surface-container-high border border-{$postsCardBorderColor}/{$postsCardBorderOpacity} posts-card">
<div class="flex justify-between items-start mb-6">
<div class="w-16 h-16 bg-surface-container-highest flex items-center justify-center rounded-lg group-hover:scale-110 transition-transform duration-500">
<span class="material-symbols-outlined text-4xl text-primary" data-icon="article">article</span>
</div>
</div>
<h3 class="font-headline text-3xl font-bold mb-4"><a href="{Url::post($p->id)}" class="hover:text-primary-container transition-colors">{$p->title}</a></h3>
<p class="text-on-surface-variant leading-relaxed line-clamp-3">{Posts::generateExcerpt($p->content, 150)|noescape}</p>
<div class="mt-6 flex items-center justify-between">
<span class="text-xs text-primary-container font-bold uppercase tracking-wider">{date('M d, Y', strtotime($p->date))}</span>
<a href="{Url::post($p->id)}" class="inline-flex items-center gap-2 text-primary-container hover:text-primary transition-colors">
Read More <span class="material-symbols-outlined text-sm">arrow_forward</span>
</a>
</div>
</article>
{/foreach}
{else}
<div class="col-span-full text-center py-20">
<p class="text-2xl text-on-surface-variant">No posts found yet. Check back soon!</p>
</div>
{/if}
</div>

<!-- Pagination -->
{if isset($data['paging'])}<div class="mt-16">{$data['paging']|noescape}</div>{/if}
</div>
</section>
