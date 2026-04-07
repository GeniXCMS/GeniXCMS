{*
Madxion Theme Header - Latte Template
Uses global MADXION_THEME_OPTIONS from function.php
*}<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$data['title'] ?? Site::$name}</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    {$site_meta|noescape}
    <style>
        :root {
            --primary-color: #ffb4a8;
            --primary-container: #d20000;
            --secondary-color: #ffb956;
            --secondary-container: #ca8500;
            --background-color: #131313;
            --surface-color: #131313;
            --surface-container-low: #1c1b1b;
            --surface-container: #201f1f;
            --surface-container-high: #2a2a2a;
            --text-color: #e5e2e1;
            --text-variant: #e8bcb5;
            --headline-font: "Space Grotesk", sans-serif;
            --body-font: "Manrope", sans-serif;
        }

        * {
            --tw-ring-color: rgba(211, 0, 0, 0.5);
        }

        html, body {
            font-family: var(--body-font);
            background-color: var(--background-color) !important;
            color: var(--text-color) !important;
        }
        h1, h2, h3, .font-headline {
            font-family: var(--headline-font);
        }
        h1 { font-size: 48px; }
        h2 { font-size: 36px; }
        h3 { font-size: 28px; }

        .glass-panel { backdrop-filter: blur(40px); }
        .kinetic-gradient {
            background: linear-gradient(135deg, var(--primary-container) 0%, var(--primary-container) 100%);
        }
        .kinetic-gradient-text {
            background: linear-gradient(to right, var(--primary-color), var(--primary-container));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        /* Dynamic color classes */
        .bg-primary { background-color: var(--primary-color) !important; }
        .bg-primary-container { background-color: var(--primary-container) !important; }
        .bg-secondary { background-color: var(--secondary-color) !important; }
        .bg-secondary-container { background-color: var(--secondary-container) !important; }
        .bg-background { background-color: var(--background-color) !important; }
        .bg-surface { background-color: var(--surface-color) !important; }
        .bg-surface-container { background-color: var(--surface-container) !important; }
        .bg-surface-container-low { background-color: var(--surface-container-low) !important; }
        .bg-surface-container-high { background-color: var(--surface-container-high) !important; }

        .text-primary { color: var(--primary-color) !important; }
        .text-primary-container { color: var(--primary-container) !important; }
        .text-secondary { color: var(--secondary-color) !important; }
        .text-on-surface { color: var(--text-color) !important; }
        .text-on-surface-variant { color: var(--text-variant) !important; }

        .border-primary-container { border-color: var(--primary-container) !important; }

        @keyframes madxion-fade-in {
            from { opacity: 0; transform: translateY(18px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: madxion-fade-in 0.9s ease-out both; }
    </style>
    {Site::loadLibHeader()|noescape}
</head>
<body>
<!-- TopNavBar -->
{php $navbarBg = MadxionTheme::get('navbar_transparent') === 'on' ? 'rgba(19, 19, 19, 0.25)' : 'rgba(19, 19, 19, 0.85)';}
<nav class="fixed top-0 w-full z-50 backdrop-blur-xl shadow-2xl shadow-red-900/5" style="background-color: {$navbarBg};">
<div class="flex justify-between items-center w-full px-8 py-6 max-w-[1440px] mx-auto">
<div class="flex items-center gap-4">
{php $logo = MadxionTheme::get('site_logo');}
{if isset($logo) && !empty($logo)}
<img src="{$logo}" alt="{Site::$name}" class="h-8 w-auto">
{/if}
<span class="text-2xl font-bold tracking-tighter text-white uppercase" style="font-family: var(--headline-font);">{Site::$name}</span>
</div>
<div class="hidden md:flex items-center gap-10">
    {Menus::getMenu('mainmenu', 'flex flex-row gap-8 items-center', true, 'block px-4 py-2 hover:text-primary-container transition-colors')|noescape}
</div>
{php $ctaLabel = MadxionTheme::get('header_cta_label', 'Get in Touch'); $ctaUrl = MadxionTheme::get('header_cta_url', '#contact');}
<a href="{$ctaUrl|noescape}" class="kinetic-gradient text-white px-6 py-2.5 rounded-xl font-bold text-sm scale-95 active:scale-90 transition-transform uppercase tracking-wider" style="font-family: 'Manrope', sans-serif;">{$ctaLabel|noescape}</a>
</div>
</nav>
<main class="pt-32 overflow-hidden">
