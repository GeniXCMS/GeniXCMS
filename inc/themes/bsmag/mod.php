<!-- MOD PAGE: PREMIUM BSMAG MAGAZINE DESIGN -->
<div class="row g-5 mb-5 align-items-start">
    <div class="col-md-8">
        <div class="mod-wrapper shadow-sm border rounded-4 bg-white overflow-hidden">
            <!-- Header Section with Magazine Aesthetic -->
            <div class="mod-header-box p-4 p-md-5 bg-body-tertiary border-bottom border-light-subtle">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb x-small text-uppercase tracking-wider fw-bold">
                        <li class="breadcrumb-item"><a href="{$site_url}" class="text-decoration-none text-secondary">Home</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Module</li>
                    </ol>
                </nav>
                <h1 class="display-3 fw-bold font-display text-dark mb-0">
                    {Mod::getTitle($mod)}
                </h1>
                <div class="mt-3 d-flex align-items-center gap-2">
                    <span class="badge rounded-pill text-bg-primary px-3 py-2">Official Module</span>
                    <span class="text-muted small">&bullet; Powered by {$platform_name}</span>
                </div>
            </div>

            <!-- Content Area -->
            <div class="mod-body-content p-4 p-md-5 fs-5 lh-lg text-secondary-emphasis">
                <div class="entry-content">
                    {Hooks::run('mod_control', $data)|noescape}
                </div>
            </div>
        </div>
    </div>

    <!-- Magazine Sidebar -->
    <aside class="col-md-4 position-sticky" style="top: 2rem;">
        {include 'sidebar.latte'}
    </aside>
</div>

<style>
    .font-display { font-family: 'Playfair Display', serif; }
    .x-small { font-size: 0.7rem; }
    .tracking-wider { letter-spacing: 0.1em; }
    
    .mod-wrapper { transition: transform 0.3s ease, box-shadow 0.3s ease; }
    
    .mod-body-content { word-wrap: break-word; }
    .mod-body-content img { 
        max-width: 100%; 
        height: auto; 
        border-radius: 1rem; 
        box-shadow: 0 8px 30px rgba(0,0,0,0.06);
        margin: 2rem 0;
        display: block;
    }
    
    .mod-body-content h1, 
    .mod-body-content h2, 
    .mod-body-content h3 {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        margin-top: 2.5rem;
        margin-bottom: 1.25rem;
        color: var(--bs-emphasis-color);
    }

    .mod-body-content p { margin-bottom: 1.8rem; }
    
    .mod-body-content a:not(.btn) {
        color: var(--bs-primary);
        text-decoration: none;
        border-bottom: 2px solid rgba(13, 110, 253, 0.12);
        transition: all 0.2s ease;
        padding-bottom: 1px;
    }
    .mod-body-content a:not(.btn):hover {
        border-bottom-color: var(--bs-primary);
        background: rgba(13, 110, 253, 0.05);
        color: #000;
    }

    /* Table support in modules */
    .mod-body-content table {
        width: 100%;
        margin-bottom: 2rem;
        border-collapse: separate;
        border-spacing: 0;
        border: 1px solid #dee2e6;
        border-radius: 0.75rem;
        overflow: hidden;
    }
    .mod-body-content th { background: #f8fafc; padding: 12px 15px; font-weight: 700; }
    .mod-body-content td { padding: 12px 15px; border-top: 1px solid #dee2e6; }
</style>
