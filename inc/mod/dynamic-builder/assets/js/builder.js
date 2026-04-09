(function () {
    if (typeof window.dynamicBuilderConfig === 'undefined') {
        window.dynamicBuilderConfig = {
            siteUrl: window.location.origin + '/',
            apiEndpoint: '/index.php?ajax=api&token=public&action=recent_posts&num=3',
            ajaxToken: '',
            elfinderUrl: '',
            isSmartUrl: false,
        };
    }

    if (typeof window.dynamicBuilderBlocks === 'undefined') {
        window.dynamicBuilderBlocks = [];
    }

    function getCurrentEditorContent() {
        let content = '';
        console.log('[Builder] === Starting Content Extraction ===');

        // 1. Check Summernote safely via its DOM
        if (window.jQuery && window.jQuery('.note-editable').length) {
            console.log('[Builder] Detecting Summernote (.note-editable)');
            content = window.jQuery('.note-editable').first().html();
        }

        // 2. Check New GxEditor modular system
        else if (window.GxEditor && window.GxEditor._editors && window.GxEditor._editors.length > 0) {
            console.log('[Builder] Detecting GxEditor object');
            content = window.GxEditor._editors[0].textarea.value;
        }

        // 3. Fallback to Raw Textarea Value
        else {
            const primaryEl = document.getElementById('primary_editor') || document.querySelector('.editor');
            if (primaryEl) {
                console.log('[Builder] Detecting primary raw textarea:', primaryEl.id || primaryEl.className);
                content = primaryEl.value;
            }
        }

        // Extract stored CSS/JS
        const cssEl = document.getElementById('gx_builder_css');
        const jsEl = document.getElementById('gx_builder_js');
        const css = cssEl ? cssEl.value : '';
        const js = jsEl ? jsEl.value : '';

        // 4. Extract master HTML if available (Source of Truth for Builder)
        const htmlMasterEl = document.getElementById('gx_builder_html');
        const htmlMaster = (htmlMasterEl && htmlMasterEl.value.trim() !== '') ? htmlMasterEl.value : content;

        console.log('[Builder] === Extraction Complete ===');
        
        let finalHtml = htmlMaster || '';
        if (typeof window.shortcodeToHtml === 'function') {
            finalHtml = window.shortcodeToHtml(finalHtml);
        }

        return {
            html: finalHtml,
            css: css || '',
            js: js || ''
        };
    }

    function exportContent(html, css, js) {
        // 1. Update Hidden Param Fields
        const cssEl = document.getElementById('gx_builder_css');
        const jsEl = document.getElementById('gx_builder_js');
        const htmlMasterEl = document.getElementById('gx_builder_html');

        if (cssEl) cssEl.value = css || '';
        if (jsEl) jsEl.value = js || '';
        if (htmlMasterEl) htmlMasterEl.value = html || '';

        // 2. Clean HTML and convert to shortcodes if possible
        var cleanHtml = (html || '')

        // Convert to shortcodes FIRST so they can be protected if needed
        if (typeof window.htmlToShortcode === 'function') {
            cleanHtml = window.htmlToShortcode(cleanHtml);
        }

        cleanHtml = cleanHtml.trim();

        // 3. Push Clean HTML/Shortcodes to Main Editor

        // 3. Push Clean HTML/Shortcodes to Main Editor
        if (window.jQuery && window.jQuery('.note-editable').length) {
            try {
                window.jQuery('.editor').first().summernote('code', cleanHtml);
            } catch (e) {
                window.jQuery('.note-editable').first().html(cleanHtml);
            }
        }
        else if (window.GxEditor && window.GxEditor._editors && window.GxEditor._editors.length > 0) {
            window.GxEditor._editors.forEach(function(ed) {
                ed.textarea.value = cleanHtml;
                ed.textarea.dispatchEvent(new Event('change'));
            });
        }

        // Always sync the underlying value
        const primaryEl = document.getElementById('primary_editor') || document.querySelector('.editor');
        if (primaryEl) {
            primaryEl.value = cleanHtml;
        }
    }

    function getPremiumBlocks() {
        return [
            // UTILITIES / BASICS (TOP PRIORITY)
            {
                id: 'heading-custom',
                label: '<i class="bi bi-type-h1 fs-4 d-block mb-1"></i>Large Heading',
                category: 'Basics',
                content: { type: 'heading-component' }
            },
            {
                id: 'text-paragraph',
                label: '<i class="bi bi-justify-left fs-4 d-block mb-1"></i>Text block',
                category: 'Basics',
                content: '<p class="text-secondary lh-lg">Insert your professional text content here. This block is designed for readability and clean layout.</p>'
            },
            {
                id: 'text-lead',
                label: '<i class="bi bi-card-text fs-4 d-block mb-1"></i>Lead Text',
                category: 'Basics',
                content: '<p class="lead text-dark opacity-75 fw-medium">An elegant lead paragraph for highlighting key information or summaries at the start of sections.</p>'
            },
            {
                id: 'image-custom',
                label: '<i class="bi bi-image fs-4 d-block mb-1"></i>Image',
                category: 'Basics',
                content: {
                    type: 'image',
                    style: { 
                        width: '100%', 
                        height: 'auto', 
                        float: 'none', 
                        'border-radius': '0',
                        'object-fit': 'cover'
                    }
                }
            },
            {
                id: 'bs-button',
                label: '<i class="bi bi-hand-index-thumb fs-4 d-block mb-1"></i>Action Button',
                category: 'Basics',
                content: '<a href="#" class="btn btn-primary rounded-pill px-4 py-2 fw-bold text-decoration-none d-inline-block">Click Here</a>'
            },
            {
                id: 'icon-material',
                label: '<i class="bi bi-star-fill fs-4 d-block mb-1"></i>Material Icon',
                category: 'Basics',
                content: '<span class="material-symbols-outlined fs-2 text-primary">feature_search</span>'
            },
            {
                id: 'icon-bootstrap',
                label: '<i class="bi bi-bootstrap fs-4 d-block mb-1"></i>BS Icon',
                category: 'Basics',
                content: { type: 'bs-icon-component' }
            },
            {
                id: 'section-container',
                label: '<i class="bi bi-layout-sidebar fs-4 d-block mb-1"></i>Section Wrapper',
                category: 'Basics',
                content: '<section class="py-5"><div class="container"><div class="p-5 bg-light rounded-5 text-center">Your content goes here...</div></div></section>'
            },
            {
                id: 'grid-2-cols',
                label: '<i class="bi bi-columns-gap fs-4 d-block mb-1"></i>2 Columns',
                category: 'Basics',
                content: {
                    type: 'row',
                    attributes: { class: 'row g-4' },
                    style: { 'min-height': '50px' },
                    components: [
                        {
                            type: 'column',
                            attributes: { class: 'col-md-6' },
                            style: { },
                            components: [{ content: '<div class="p-4 bg-light rounded-4 text-center">Column 1</div>' }]
                        },
                        {
                            type: 'column',
                            attributes: { class: 'col-md-6' },
                            style: { },
                            components: [{ content: '<div class="p-4 bg-light rounded-4 text-center">Column 2</div>' }]
                        }
                    ]
                }
            },
            {
                id: 'grid-3-cols',
                label: '<i class="bi bi-grid-3x2 fs-4 d-block mb-1"></i>3 Columns',
                category: 'Basics',
                content: {
                    type: 'row',
                    attributes: { class: 'row g-4' },
                    style: { 'min-height': '50px' },
                    components: [
                        {
                            type: 'column',
                            attributes: { class: 'col-md-4' },
                            style: { },
                            components: [{ content: '<div class="p-4 bg-light rounded-4 text-center">Col 1</div>' }]
                        },
                        {
                            type: 'column',
                            attributes: { class: 'col-md-4' },
                            style: { },
                            components: [{ content: '<div class="p-4 bg-light rounded-4 text-center">Col 2</div>' }]
                        },
                        {
                            type: 'column',
                            attributes: { class: 'col-md-4' },
                            style: { },
                            components: [{ content: '<div class="p-4 bg-light rounded-4 text-center">Col 3</div>' }]
                        }
                    ]
                }
            },
            {
                id: 'bs-card',
                label: '<i class="bi bi-card-image fs-4 d-block mb-1"></i>Content Card',
                category: 'Basics',
                content: {
                    type: 'content-card-system',
                    attributes: { class: 'card border-0 shadow-sm rounded-4 overflow-hidden h-100 content-card-wrapper' },
                    components: [
                        {
                            tagName: 'img',
                            attributes: { 
                                src: 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&q=80&w=600', 
                                class: 'card-img-top', 
                                alt: 'Card cap' 
                            }
                        },
                        {
                            tagName: 'div',
                            attributes: { class: 'card-body p-4' },
                            components: [
                                { tagName: 'h5', attributes: { class: 'card-title fw-bold' }, content: 'Card Title' },
                                { tagName: 'p', attributes: { class: 'card-text text-secondary small' }, content: 'Some quick example text to build on the card title and make up the bulk of the card\'s content.' },
                                { tagName: 'a', attributes: { href: '#', class: 'btn btn-primary rounded-pill px-4 btn-sm' }, content: 'Read More' }
                            ]
                        }
                    ]
                }
            },
            {
                id: 'text-icon-flex',
                label: '<i class="bi bi-check-circle fs-4 d-block mb-1"></i>Point Item',
                category: 'Basics',
                content: `
                <div class="d-flex align-items-center">
                    <i class="bi bi-check2-circle text-primary fs-4 me-3"></i>
                    <p class="mb-0 text-dark fw-semibold">Key benefit or feature point goes here...</p>
                </div>`
            },
            {
                id: 'text-blockquote',
                label: '<i class="bi bi-chat-quote fs-4 d-block mb-1"></i>Quote Block',
                category: 'Basics',
                content: `
                <blockquote class="blockquote border-start border-4 border-primary ps-4 py-2 bg-light bg-opacity-50 rounded-end-4">
                    <p class="mb-2 italic fs-5 fw-bold text-dark">"Creativity is intelligence having fun."</p>
                    <footer class="blockquote-footer small">Albert Einstein</footer>
                </blockquote>`
            },
            {
                id: 'progress-bar-system',
                label: '<i class="bi bi-reception-3 fs-4 d-block mb-1"></i>Progress Bar',
                category: 'Basics',
                content: {
                    type: 'bootstrap-progress-container',
                    components: [
                        { tagName: 'label', attributes: { class: 'form-label fw-bold small mb-2' }, content: 'Skill Proficiency' },
                        { type: 'bootstrap-progress-item' }
                    ]
                }
            },
            {
                id: 'separator-custom',
                label: '<i class="bi bi-hr fs-4 d-block mb-1"></i>Separator',
                category: 'Basics',
                content: '<hr class="border-2 opacity-25 w-100 mx-auto">'
            },
            {
                id: 'video-embed',
                label: '<i class="bi bi-play-circle fs-4 d-block mb-1"></i>Video Player',
                category: 'Basics',
                content: `
                    <div class="ratio ratio-16x9 rounded-4 overflow-hidden shadow-sm mb-4 border border-light">
                        <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>`
            },
            {
                id: 'google-maps',
                label: '<i class="bi bi-geo-alt fs-4 d-block mb-1"></i>Map Embed',
                category: 'Basics',
                content: `
                    <div class="ratio ratio-21x9 rounded-4 overflow-hidden shadow-sm mb-4 border border-light">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3153.0192347313023!2d-122.41941548468205!3d37.77492957975949!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8085809c6c8f4459%3A0xb10ed6d9b5050c62!2sMarket%20St%2C%20San%20Francisco%2C%20CA%2C%20USA!5e0!3m2!1sen!2sid!4v1625070000000!5m2!1sen!2sid" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>`
            },
            {
                id: 'social-links-bar',
                label: '<i class="bi bi-share fs-4 d-block mb-1"></i>Social Pins',
                category: 'Basics',
                content: `
                <div class="d-flex gap-3 align-items-center p-3">
                    <a href="#" class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 40px; height: 40px;"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="btn btn-info text-white rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 40px; height: 40px;"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="btn btn-danger rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 40px; height: 40px;"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="btn btn-dark rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 40px; height: 40px;"><i class="bi bi-linkedin"></i></a>
                </div>`
            },
            {
                id: 'pricing-comparison-table',
                label: '<i class="bi bi-table fs-4 d-block mb-1"></i>Compare Table',
                category: 'Basics',
                content: {
                    type: 'comparison-table',
                    content: `
                    <div class="table-responsive rounded-4 overflow-hidden shadow-sm border border-light">
                        <table class="table table-hover align-middle mb-0 bg-white">
                            <thead class="bg-light text-dark">
                                <tr>
                                    <th class="py-4 ps-4 border-0" data-gjs-type="text">Features</th>
                                    <th class="py-4 text-center border-0" style="width: 200px;" data-gjs-type="text">Basic</th>
                                    <th class="py-4 text-center border-0 text-primary" style="width: 200px;" data-gjs-type="text">Pro</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="ps-4 fw-medium" data-gjs-type="text">Core Components</td>
                                    <td class="text-center" data-gjs-type="default"><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                                    <td class="text-center" data-gjs-type="default"><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                                </tr>
                                <tr>
                                    <td class="ps-4 fw-medium" data-gjs-type="text">Cloud Storage</td>
                                    <td class="text-center text-secondary small" data-gjs-type="text">2GB</td>
                                    <td class="text-center text-primary fw-bold" data-gjs-type="text">10GB</td>
                                </tr>
                                <tr>
                                    <td class="ps-4 fw-medium" data-gjs-type="text">Automation API</td>
                                    <td class="text-center" data-gjs-type="default"><i class="bi bi-x-circle text-danger opacity-25"></i></td>
                                    <td class="text-center" data-gjs-type="default"><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>`
                }
            },
            {
                id: 'testimonial-card',
                label: '<i class="bi bi-chat-left-quote fs-4 d-block mb-1"></i>Feedback Card',
                category: 'Basics',
                content: `
                <div class="p-5 bg-white rounded-5 shadow-sm border border-light text-center h-100">
                    <img src="https://i.pravatar.cc/150?u=antigravity" class="rounded-circle mb-4 border border-5 border-light shadow-sm" width="100" height="100">
                    <div class="mb-4 text-warning">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                    <p class="fs-5 text-dark italic mb-4">"The platform exceeded all my expectations. The user interface is intuitive and the results were immediate."</p>
                    <h5 class="fw-bold mb-1">Sarah McArthur</h5>
                    <p class="small text-secondary text-uppercase tracking-widest">CEO @ TechFlow</p>
                </div>`
            },
            {
                id: 'icon-card',
                label: '<i class="bi bi-star-fill fs-4 d-block mb-1"></i>Icon Card',
                category: 'Basics',
                content: `
                <div class="text-center p-4 rounded-4 bg-white shadow-sm border border-light" style="transition: all 0.3s ease;">
                    <div class="mb-3 d-flex align-items-center justify-content-center mx-auto rounded-circle bg-primary bg-opacity-10" style="width: 64px; height: 64px;">
                        <i class="bi bi-lightning-charge-fill text-primary fs-2"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Card Title</h5>
                    <p class="text-secondary small mb-0">Write a short description about this feature or benefit. Keep it clear and concise.</p>
                </div>`
            },

            // HERO SECTIONS
            {
                id: 'hero-cyber-frontier',
                label: '<i class="bi bi-cpu fs-4 d-block mb-1"></i>Cyber Hero',
                category: 'Hero Styles',
                content: {
                    tagName: 'section',
                    attributes: { class: 'position-relative min-vh-100 d-flex align-items-center py-5 overflow-hidden bg-black text-white cyber-hero-section' },
                    style: { },
                    components: [
                        { 
                            tagName: 'style', 
                            content: `
                                .cyber-hero-section { --primary-glow: rgba(210, 0, 0, 0.4); --secondary-glow: rgba(37, 99, 235, 0.2); }
                                .blur-large { filter: blur(120px); pointer-events: none; }
                                .glass-card { backdrop-filter: blur(25px); -webkit-backdrop-filter: blur(25px); border: 1px solid rgba(255,255,255,0.1) !important; background: rgba(0,0,0,0.6) !important; }
                                .tracking-widest { letter-spacing: 0.3em !important; }
                                .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8) !important; }
                                .cyber-grid { pointer-events: none; }
                                .kinetic-gradient { background: linear-gradient(135deg, #d20000 0%, #ff4d4d 100%); transition: all 0.3s ease; }
                                .kinetic-gradient:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(210, 0, 0, 0.4); brightness: 1.1; }
                            ` 
                        },
                        {
                            tagName: 'div',
                            attributes: { class: 'container-xxl position-relative' },
                            style: { 'z-index': 10 },
                            components: [{
                                type: 'row',
                                attributes: { class: 'row align-items-center g-5' },
                                components: [
                                    {
                                        type: 'column',
                                        attributes: { class: 'col-lg-6' },
                                        components: [
                                            { type: 'text', tagName: 'span', attributes: { class: 'd-inline-block text-danger mb-4 fw-bold text-uppercase tracking-widest small' }, content: '01 // THE DIGITAL FRONTIER' },
                                            { type: 'text', tagName: 'h1', attributes: { class: 'display-1 fw-bold tracking-tight mb-4' }, style: { 'line-height': '0.95', 'font-size': 'calc(2.8rem + 4.5vw)' }, content: 'Revolutionizing <br> <span class="text-danger" style="text-shadow: 0 0 30px rgba(210,0,0,0.5);">IT Solutions</span> <br> for the Digital Age.' },
                                            { type: 'text', tagName: 'p', attributes: { class: 'lead mb-5 opacity-75 fw-light' }, style: { 'max-width': '550px', 'font-size': '1.25rem', 'line-height': '1.8' }, content: 'We architect elite digital infrastructures that transform complex challenges into competitive advantages. Powering the next generation of global enterprises.' },
                                            {
                                                tagName: 'div',
                                                attributes: { class: 'd-flex flex-wrap gap-3' },
                                                components: [
                                                    { 
                                                        tagName: 'button', 
                                                        attributes: { class: 'btn btn-danger btn-lg px-5 py-4 rounded-4 fw-bold border-0 shadow-lg d-flex align-items-center gap-3 kinetic-gradient' }, 
                                                        components: [
                                                            { type: 'text', content: 'Get Started' },
                                                            { tagName: 'span', attributes: { class: 'material-symbols-outlined' }, content: 'arrow_forward' }
                                                        ]
                                                    },
                                                    { 
                                                        tagName: 'button', 
                                                        attributes: { class: 'btn btn-outline-light btn-lg px-5 py-4 rounded-4 fw-bold opacity-75 hover-bg-light' }, 
                                                        components: [{ type: 'text', content: 'View Ecosystem' }]
                                                    }
                                                ]
                                            }
                                        ]
                                    },
                                    {
                                        type: 'column',
                                        attributes: { class: 'col-lg-6' },
                                        components: [{
                                            tagName: 'div',
                                            attributes: { class: 'position-relative p-4' },
                                            components: [
                                                {
                                                    tagName: 'div',
                                                    attributes: { class: 'ratio ratio-1x1 rounded-5 overflow-hidden shadow-2xl border border-white border-opacity-10 bg-dark bg-opacity-50' },
                                                    components: [{ tagName: 'div', attributes: { class: 'd-flex align-items-center justify-content-center h-100' }, components: [{ tagName: 'i', attributes: { class: 'bi bi-command text-danger display-1' } }] }]
                                                },
                                                {
                                                    tagName: 'div',
                                                    attributes: { class: 'position-absolute bottom-0 start-0 mb-n2 ms-n2 p-4 rounded-4 glass-card shadow-2xl' },
                                                    style: { 'max-width': '320px' },
                                                    components: [
                                                        {
                                                            tagName: 'div',
                                                            attributes: { class: 'd-flex gap-2 mb-3' },
                                                            components: [
                                                                { tagName: 'span', attributes: { class: 'rounded-circle' }, style: { width: '10px', height: '10px', background: '#d20000', 'box-shadow': '0 0 10px #d20000' } },
                                                                { tagName: 'span', attributes: { class: 'rounded-circle bg-primary opacity-75' }, style: { width: '10px', height: '10px' } },
                                                                { tagName: 'span', attributes: { class: 'rounded-circle bg-secondary opacity-50' }, style: { width: '10px', height: '10px' } }
                                                            ]
                                                        },
                                                        { type: 'text', tagName: 'p', attributes: { class: 'h5 fw-bold text-white mb-2' }, content: '99.9% Uptime' },
                                                        { type: 'text', tagName: 'p', attributes: { class: 'small text-white opacity-75 mb-0 lh-base' }, content: 'Engineered for absolute resilience in mission-critical environments.' }
                                                    ]
                                                }
                                            ]
                                        }]
                                    }
                                ]
                            }]
                        }
                    ]
                }
            },
            {
                id: 'hero-centered',
                label: '<i class="bi bi-megaphone fs-4 d-block mb-1"></i>Centered Hero',
                category: 'Hero Styles',
                content: {
                    tagName: 'section',
                    attributes: { class: 'py-5 py-xl-8 background-light' },
                    style: { },
                    components: [{
                        tagName: 'div',
                        attributes: { class: 'container' },
                        components: [{
                            type: 'row',
                            attributes: { class: 'row justify-content-md-center' },
                            components: [{
                                type: 'column',
                                attributes: { class: 'col-12 col-md-10 col-lg-8 col-xl-7 col-xxl-6 text-center' },
                                components: [
                                    { tagName: 'h1', attributes: { class: 'display-4 fw-bold mb-4' }, content: 'Capturing the Essence of Your Vision' },
                                    { tagName: 'p', attributes: { class: 'lead mb-5 text-secondary' }, content: 'Discover unique and impactful perspectives that bring your brand\'s story to life with our creative solutions.' },
                                    {
                                        tagName: 'div',
                                        attributes: { class: 'd-grid gap-2 d-sm-flex justify-content-sm-center' },
                                        components: [
                                            { tagName: 'button', attributes: { type: 'button', class: 'btn btn-primary btn-lg px-4 gap-3' }, content: 'Get Started' },
                                            { tagName: 'button', attributes: { type: 'button', class: 'btn btn-outline-secondary btn-lg px-4' }, content: 'Learn More' }
                                        ]
                                    }
                                ]
                            }]
                        }]
                    }]
                }
            },
            {
                id: 'hero-split',
                label: '<i class="bi bi-layout-split fs-4 d-block mb-1"></i>Split Hero',
                category: 'Hero Styles',
                content: {
                    tagName: 'section',
                    attributes: { class: 'py-5' },
                    style: { },
                    components: [{
                        tagName: 'div',
                        attributes: { class: 'container' },
                        components: [{
                            type: 'row',
                            attributes: { class: 'row align-items-center g-5' },
                            components: [
                                {
                                    type: 'column',
                                    attributes: { class: 'col-lg-6' },
                                    components: [
                                        { tagName: 'h1', attributes: { class: 'display-5 fw-bold lh-1 mb-3' }, content: 'Modern Solutions for Your Growing Business' },
                                        { tagName: 'p', attributes: { class: 'lead text-secondary mb-4' }, content: 'Quickly design and customize responsive mobile-first sites with Bootstrap, the world’s most popular front-end open source toolkit.' },
                                        {
                                            tagName: 'div',
                                            attributes: { class: 'd-grid gap-2 d-md-flex justify-content-md-start' },
                                            components: [
                                                { tagName: 'button', attributes: { type: 'button', class: 'btn btn-primary btn-lg px-4 me-md-2' }, content: 'Start Project' },
                                                { tagName: 'button', attributes: { type: 'button', class: 'btn btn-light btn-lg px-4' }, content: 'View Gallery' }
                                            ]
                                        }
                                    ]
                                },
                                {
                                    type: 'column',
                                    attributes: { class: 'col-lg-6 text-center' },
                                    components: [{
                                        tagName: 'img',
                                        attributes: { 
                                            src: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=800', 
                                            class: 'd-block mx-lg-auto img-fluid rounded-5 shadow-lg',
                                            width: '700',
                                            height: '500',
                                            loading: 'lazy'
                                        }
                                    }]
                                }
                            ]
                        }]
                    }]
                }
            },

            // FEATURES
            {
                id: 'features-grid',
                label: '<i class="bi bi-grid-3x3-gap fs-4 d-block mb-1"></i>Features Grid',
                category: 'Standard Sections',
                content: {
                    tagName: 'section',
                    attributes: { class: 'py-5' },
                    style: { },
                    components: [{
                        tagName: 'div',
                        attributes: { class: 'container' },
                        components: [
                            {
                                tagName: 'div',
                                attributes: { class: 'text-center mb-5' },
                                components: [
                                    { tagName: 'h2', attributes: { class: 'fw-bold' }, content: 'Our Core Features' },
                                    { tagName: 'p', attributes: { class: 'text-secondary' }, content: 'Everything you need to succeed in one place.' }
                                ]
                            },
                            {
                                type: 'row',
                                attributes: { class: 'row g-4 py-5 row-cols-1 row-cols-lg-3' },
                                components: [
                                    {
                                        type: 'column',
                                        attributes: { class: 'col d-flex align-items-start' },
                                        components: [
                                            { tagName: 'div', attributes: { class: 'icon-square bg-primary bg-opacity-10 text-primary flex-shrink-0 me-3 rounded-4 d-flex align-items-center justify-content-center', style: 'width: 3rem; height: 3rem;' }, components: [{ tagName: 'i', attributes: { class: 'bi bi-lightning-charge-fill fs-4' } }] },
                                            { tagName: 'div', components: [{ tagName: 'h3', attributes: { class: 'fs-5 fw-bold' }, content: 'Fast Performance' }, { tagName: 'p', attributes: { class: 'text-secondary small' }, content: 'Optimized core ensures your website loads in under a second for the best user experience.' }] }
                                        ]
                                    },
                                    {
                                        type: 'column',
                                        attributes: { class: 'col d-flex align-items-start' },
                                        components: [
                                            { tagName: 'div', attributes: { class: 'icon-square bg-success bg-opacity-10 text-success flex-shrink-0 me-3 rounded-4 d-flex align-items-center justify-content-center', style: 'width: 3rem; height: 3rem;' }, components: [{ tagName: 'i', attributes: { class: 'bi bi-shield-check fs-4' } }] },
                                            { tagName: 'div', components: [{ tagName: 'h3', attributes: { class: 'fs-5 fw-bold' }, content: 'Enterprise Security' }, { tagName: 'p', attributes: { class: 'text-secondary small' }, content: 'Bank-grade security protocols keep your data and your users safe from external threats.' }] }
                                        ]
                                    },
                                    {
                                        type: 'column',
                                        attributes: { class: 'col d-flex align-items-start' },
                                        components: [
                                            { tagName: 'div', attributes: { class: 'icon-square bg-info bg-opacity-10 text-info flex-shrink-0 me-3 rounded-4 d-flex align-items-center justify-content-center', style: 'width: 3rem; height: 3rem;' }, components: [{ tagName: 'i', attributes: { class: 'bi bi-cpu fs-4' } }] },
                                            { tagName: 'div', components: [{ tagName: 'h3', attributes: { class: 'fs-5 fw-bold' }, content: 'Automation API' }, { tagName: 'p', attributes: { class: 'text-secondary small' }, content: 'Connect all your favorite tools seamlessly with our robust and well-documented API.' }] }
                                        ]
                                    }
                                ]
                            }
                        ]
                    }]
                }
            },

            // CTA
            {
                id: 'cta-banner',
                label: '<i class="bi bi-megaphone fs-4 d-block mb-1"></i>CTA Banner',
                category: 'Standard Sections',
                content: {
                    tagName: 'section',
                    attributes: { class: 'py-5' },
                    style: { },
                    components: [{
                        tagName: 'div',
                        attributes: { class: 'container' },
                        components: [{
                            tagName: 'div',
                            attributes: { class: 'p-5 text-center bg-primary rounded-5 shadow-lg text-white' },
                            components: [
                                { tagName: 'h1', attributes: { class: 'fw-bold' }, content: 'Ready to Launch Your Brand?' },
                                { tagName: 'p', attributes: { class: 'col-lg-8 mx-auto lead mb-4 opacity-75' }, content: 'Join over 10,000+ businesses who trust GeniXCMS for their digital presence. Start your 14-day free trial today.' },
                                {
                                    tagName: 'div',
                                    attributes: { class: 'd-grid gap-2 d-sm-flex justify-content-sm-center' },
                                    components: [
                                        { tagName: 'button', attributes: { type: 'button', class: 'btn btn-light btn-lg px-4 rounded-pill fw-bold' }, content: 'Get Started Now' },
                                        { tagName: 'button', attributes: { type: 'button', class: 'btn btn-outline-light btn-lg px-4 rounded-pill' }, content: 'Contact Sales' }
                                    ]
                                }
                            ]
                        }]
                    }]
                }
            },

            // PRICING
            {
                id: 'pricing-table',
                label: '<i class="bi bi-tags fs-4 d-block mb-1"></i>Pricing Table',
                category: 'Standard Sections',
                content: {
                    tagName: 'section',
                    attributes: { class: 'py-5 bg-light' },
                    style: { },
                    components: [{
                        tagName: 'div',
                        attributes: { class: 'container' },
                        components: [{
                            type: 'row',
                            attributes: { class: 'row row-cols-1 row-cols-md-3 mb-3 text-center g-4' },
                            components: [
                                {
                                    type: 'column',
                                    attributes: { class: 'col' },
                                    components: [{
                                        tagName: 'div',
                                        attributes: { class: 'card mb-4 rounded-4 border-0 shadow-sm overflow-hidden h-100' },
                                        components: [
                                            { tagName: 'div', attributes: { class: 'card-header py-3 bg-white border-0' }, components: [{ tagName: 'h4', attributes: { class: 'my-0 fw-bold' }, content: 'Starter' }] },
                                            { tagName: 'div', attributes: { class: 'card-body' }, components: [
                                                { tagName: 'h1', attributes: { class: 'card-title pricing-card-title fw-bold' }, content: '$0<small class="text-muted fw-light">/mo</small>' },
                                                { tagName: 'ul', attributes: { class: 'list-unstyled mt-3 mb-4 text-secondary' }, components: [{ tagName: 'li', content: '10 users included' }, { tagName: 'li', content: '2 GB of storage' }, { tagName: 'li', content: 'Email support' }] },
                                                { tagName: 'button', attributes: { type: 'button', class: 'w-100 btn btn-lg btn-outline-primary rounded-pill' }, content: 'Sign up for free' }
                                            ] }
                                        ]
                                    }]
                                },
                                {
                                    type: 'column',
                                    attributes: { class: 'col' },
                                    components: [{
                                        tagName: 'div',
                                        attributes: { class: 'card mb-4 rounded-4 border-primary shadow-lg overflow-hidden border-2 h-100' },
                                        components: [
                                            { tagName: 'div', attributes: { class: 'card-header py-3 text-bg-primary border-primary' }, components: [{ tagName: 'h4', attributes: { class: 'my-0 fw-bold' }, content: 'Professional' }] },
                                            { tagName: 'div', attributes: { class: 'card-body' }, components: [
                                                { tagName: 'h1', attributes: { class: 'card-title pricing-card-title fw-bold' }, content: '$15<small class="text-muted fw-light">/mo</small>' },
                                                { tagName: 'ul', attributes: { class: 'list-unstyled mt-3 mb-4' }, components: [{ tagName: 'li', content: '20 users included' }, { tagName: 'li', content: '10 GB of storage' }, { tagName: 'li', content: 'Priority support' }] },
                                                { tagName: 'button', attributes: { type: 'button', class: 'w-100 btn btn-lg btn-primary rounded-pill' }, content: 'Get started' }
                                            ] }
                                        ]
                                    }]
                                },
                                {
                                    type: 'column',
                                    attributes: { class: 'col' },
                                    components: [{
                                        tagName: 'div',
                                        attributes: { class: 'card mb-4 rounded-4 border-0 shadow-sm overflow-hidden h-100' },
                                        components: [
                                            { tagName: 'div', attributes: { class: 'card-header py-3 bg-white border-0' }, components: [{ tagName: 'h4', attributes: { class: 'my-0 fw-bold' }, content: 'Enterprise' }] },
                                            { tagName: 'div', attributes: { class: 'card-body' }, components: [
                                                { tagName: 'h1', attributes: { class: 'card-title pricing-card-title fw-bold' }, content: '$29<small class="text-muted fw-light">/mo</small>' },
                                                { tagName: 'ul', attributes: { class: 'list-unstyled mt-3 mb-4 text-secondary' }, components: [{ tagName: 'li', content: 'Unlimited users' }, { tagName: 'li', content: '50 GB of storage' }, { tagName: 'li', content: 'Phone support' }] },
                                                { tagName: 'button', attributes: { type: 'button', class: 'w-100 btn btn-lg btn-primary rounded-pill' }, content: 'Contact us' }
                                            ] }
                                        ]
                                    }]
                                }
                            ]
                        }]
                    }]
                }
            },
            {
                id: 'versus-compare',
                label: '<i class="bi bi-arrow-left-right fs-4 d-block mb-1"></i>Versus (Vs)',
                category: 'Basics',
                content: `
                <div class="row align-items-center g-0 rounded-5 overflow-hidden shadow-sm border border-light">
                    <div class="col-md-5 p-5 bg-white text-center">
                        <i class="bi bi-x-diamond-fill text-danger display-4 mb-3"></i>
                        <h4 class="fw-bold text-dark">Competitor A</h4>
                        <p class="text-secondary small mb-0">Limited, legacy architecture.</p>
                    </div>
                    <div class="col-md-2 p-3 bg-light text-center" style="z-index: 2;">
                        <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center mx-auto shadow-lg fw-bold" style="width: 50px; height: 50px;">VS</div>
                    </div>
                    <div class="col-md-5 p-5 bg-primary text-white text-center">
                        <i class="bi bi-lightning-charge-fill text-white display-4 mb-3"></i>
                        <h4 class="fw-bold">Your Brand</h4>
                        <p class="text-white opacity-75 small mb-0">Elite, modern performance.</p>
                    </div>
                </div>`
            },
            {
                id: 'cta-action-minimal',
                label: '<i class="bi bi-lightning fs-4 d-block mb-1"></i>Simple CTA',
                category: 'Standard Sections',
                content: `
                <div class="p-4 bg-dark rounded-4 text-white d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="fw-bold mb-1 text-white">Start Building Today</h4>
                        <p class="mb-0 opacity-75 small">No credit card required for 14 days.</p>
                    </div>
                    <button class="btn btn-primary rounded-pill px-4">Get Started</button>
                </div>`
            },

            // FAQ
            {
                id: 'faq-accordion',
                label: '<i class="bi bi-question-circle fs-4 d-block mb-1"></i>FAQ Accordion',
                category: 'Interactive',
                content: {
                    type: 'accordion',
                    attributes: { class: 'accordion accordion-flush', id: 'accordionFlushExample' },
                    style: { padding: '20px', background: 'rgba(0,0,0,0.02)', 'border-radius': '15px' },
                    components: [
                        {
                            tagName: 'div',
                            attributes: { class: 'accordion-item border-bottom' },
                            components: [
                                { tagName: 'h2', attributes: { class: 'accordion-header' }, components: [{ tagName: 'button', attributes: { class: 'accordion-button collapsed fw-bold py-3', type: 'button', 'data-bs-toggle': 'collapse', 'data-bs-target': '#flush-one' }, content: 'How do I reset my password?' }] },
                                { tagName: 'div', attributes: { id: 'flush-one', class: 'accordion-collapse collapse', 'data-bs-parent': '#accordionFlushExample' }, components: [{ tagName: 'div', attributes: { class: 'accordion-body text-secondary' }, content: 'You can reset your password by clicking on the "Forgot Password" link on the login page.' }] }
                            ]
                        },
                        {
                            tagName: 'div',
                            attributes: { class: 'accordion-item' },
                            components: [
                                { tagName: 'h2', attributes: { class: 'accordion-header' }, components: [{ tagName: 'button', attributes: { class: 'accordion-button collapsed fw-bold py-3', type: 'button', 'data-bs-toggle': 'collapse', 'data-bs-target': '#flush-two' }, content: 'What is your refund policy?' }] },
                                { tagName: 'div', attributes: { id: 'flush-two', class: 'accordion-collapse collapse', 'data-bs-parent': '#accordionFlushExample' }, components: [{ tagName: 'div', attributes: { class: 'accordion-body text-secondary' }, content: 'We offer a 30-day money-back guarantee for all our premium plans.' }] }
                            ]
                        }
                    ]
                }
            },
            {
                id: 'process-steps',
                label: '<i class="bi bi-list-ol fs-4 d-block mb-1"></i>Process Steps',
                category: 'Interactive',
                content: {
                    type: 'process-steps',
                    attributes: { class: 'row g-4 process-container' },
                    style: { padding: '20px', background: 'rgba(0,0,0,0.01)', 'border-radius': '20px' },
                    components: [
                        {
                            type: 'column',
                            attributes: { class: 'col-md-4' },
                            components: [{
                                tagName: 'div',
                                attributes: { class: 'h-100 p-5 bg-white rounded-5 shadow-sm border border-light text-center position-relative overflow-hidden' },
                                components: [
                                    { tagName: 'div', attributes: { class: 'display-1 fw-bold opacity-10 position-absolute top-0 end-0 me-n3 mt-n3' }, content: '1' },
                                    { tagName: 'div', attributes: { class: 'btn btn-primary rounded-circle mb-4 p-0 d-flex align-items-center justify-content-center mx-auto', style: 'width:60px; height:60px;' }, components: [{ tagName: 'i', attributes: { class: 'bi bi-person-plus fs-3' } }] },
                                    { tagName: 'h3', attributes: { class: 'fs-4 fw-bold mb-3' }, content: 'Create Account' },
                                    { tagName: 'p', attributes: { class: 'text-secondary mb-0' }, content: 'Sign up in seconds.' }
                                ]
                            }]
                        },
                        {
                            type: 'column',
                            attributes: { class: 'col-md-4' },
                            components: [{
                                tagName: 'div',
                                attributes: { class: 'h-100 p-5 bg-white rounded-5 shadow-sm border border-light text-center position-relative overflow-hidden' },
                                components: [
                                    { tagName: 'div', attributes: { class: 'display-1 fw-bold opacity-10 position-absolute top-0 end-0 me-n3 mt-n3' }, content: '2' },
                                    { tagName: 'div', attributes: { class: 'btn btn-success rounded-circle mb-4 p-0 d-flex align-items-center justify-content-center mx-auto', style: 'width:60px; height:60px;' }, components: [{ tagName: 'i', attributes: { class: 'bi bi-gear fs-3' } }] },
                                    { tagName: 'h3', attributes: { class: 'fs-4 fw-bold mb-3' }, content: 'Setup Content' },
                                    { tagName: 'p', attributes: { class: 'text-secondary mb-0' }, content: 'Configure preferences.' }
                                ]
                            }]
                        },
                        {
                            type: 'column',
                            attributes: { class: 'col-md-4' },
                            components: [{
                                tagName: 'div',
                                attributes: { class: 'h-100 p-5 bg-white rounded-5 shadow-sm border border-light text-center position-relative overflow-hidden' },
                                components: [
                                    { tagName: 'div', attributes: { class: 'display-1 fw-bold opacity-10 position-absolute top-0 end-0 me-n3 mt-n3' }, content: '3' },
                                    { tagName: 'div', attributes: { class: 'btn btn-info rounded-circle mb-4 p-0 d-flex align-items-center justify-content-center mx-auto', style: 'width:60px; height:60px;' }, components: [{ tagName: 'i', attributes: { class: 'bi bi-rocket-takeoff fs-3 text-white' } }] },
                                    { tagName: 'h3', attributes: { class: 'fs-4 fw-bold mb-3' }, content: 'Go Live' },
                                    { tagName: 'p', attributes: { class: 'text-secondary mb-0' }, content: 'Publish your work.' }
                                ]
                            }]
                        }
                    ]
                }
            },

            {
                id: 'tabs-content',
                label: '<i class="bi bi-segmented-nav fs-4 d-block mb-1"></i>Tab Content',
                category: 'Interactive',
                content: {
                    type: 'tabs',
                    attributes: { class: 'tabs-container bg-white shadow-sm overflow-hidden' },
                    style: { padding: '10px' },
                    components: [
                        {
                            tagName: 'ul',
                            attributes: { class: 'nav nav-tabs border-bottom-0 bg-light p-2 gap-2', id: 'myTab', role: 'tablist' },
                            components: [
                                {
                                    tagName: 'li',
                                    attributes: { class: 'nav-item', role: 'presentation' },
                                    components: [{
                                        type: 'text',
                                        tagName: 'button',
                                        editable: true,
                                        attributes: { class: 'nav-link active fw-bold px-4 py-3 border-0 bg-transparent', id: 'home-tab', 'data-bs-toggle': 'tab', 'data-bs-target': '#home-tab-pane', type: 'button' },
                                        content: 'Main Info'
                                    }]
                                },
                                {
                                    tagName: 'li',
                                    attributes: { class: 'nav-item', role: 'presentation' },
                                    components: [{
                                        type: 'text',
                                        tagName: 'button',
                                        editable: true,
                                        attributes: { class: 'nav-link fw-bold px-4 py-3 border-0 bg-transparent', id: 'profile-tab', 'data-bs-toggle': 'tab', 'data-bs-target': '#profile-tab-pane', type: 'button' },
                                        content: 'Specifications'
                                    }]
                                }
                            ]
                        },
                        {
                            tagName: 'div',
                            attributes: { class: 'tab-content', id: 'myTabContent' },
                            components: [
                                {
                                    tagName: 'div',
                                    attributes: { class: 'tab-pane fade show active p-4', id: 'home-tab-pane', role: 'tabpanel' },
                                    components: [{ content: '<h4 class="fw-bold mb-3">Core Features</h4><p class="text-secondary">Discover our world-class features designed for performance.</p>' }]
                                },
                                {
                                    tagName: 'div',
                                    attributes: { class: 'tab-pane fade p-4', id: 'profile-tab-pane', role: 'tabpanel' },
                                    components: [{ content: '<h4 class="fw-bold mb-3">Technical Specs</h4><p class="text-secondary">Detailed specifications and requirements for integration.</p>' }]
                                }
                            ]
                        }
                    ]
                }
            },
            {
                id: 'image-slider-hero',
                label: '<i class="bi bi-images fs-4 d-block mb-1"></i>Image Slider',
                category: 'Interactive',
                content: `
                    <div id="gxSlider1" class="carousel slide rounded-5 overflow-hidden shadow-sm" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#gxSlider1" data-bs-slide-to="0" class="active" aria-current="true"></button>
                            <button type="button" data-bs-target="#gxSlider1" data-bs-slide-to="1"></button>
                            <button type="button" data-bs-target="#gxSlider1" data-bs-slide-to="2"></button>
                        </div>
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="https://images.unsplash.com/photo-1497215728101-856f4ea42174?auto=format&fit=crop&q=80&w=1200" class="d-block w-100" style="height: 450px; object-fit: cover;" alt="Slide 1">
                                <div class="carousel-caption d-none d-md-block bg-black bg-opacity-50 rounded-4 p-4 mb-4">
                                    <h3 class="fw-bold text-white">Innovation in Focus</h3>
                                    <p>Leading the way in digital transformation.</p>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&q=80&w=1200" class="d-block w-100" style="height: 450px; object-fit: cover;" alt="Slide 2">
                                <div class="carousel-caption d-none d-md-block bg-black bg-opacity-50 rounded-4 p-4 mb-4">
                                    <h3 class="fw-bold text-white">Future Architecture</h3>
                                    <p>Building tomorrow's infrastructure today.</p>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <img src="https://images.unsplash.com/photo-1551434678-e076c223a692?auto=format&fit=crop&q=80&w=1200" class="d-block w-100" style="height: 450px; object-fit: cover;" alt="Slide 3">
                                <div class="carousel-caption d-none d-md-block bg-black bg-opacity-50 rounded-4 p-4 mb-4">
                                    <h3 class="fw-bold text-white">Team Excellence</h3>
                                    <p>Collaboration that drives results.</p>
                                </div>
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#gxSlider1" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#gxSlider1" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                `
            },
            {
                id: 'image-carousel-grid',
                label: '<i class="bi bi-collection-play fs-4 d-block mb-1"></i>Image Carousel',
                category: 'Interactive',
                content: {
                    type: 'bootstrap-image-carousel',
                    tagName: 'div',
                    attributes: { class: 'gx-img-carousel position-relative overflow-hidden py-4 px-1' },
                    components: [
                        {
                            tagName: 'div',
                            attributes: { class: 'gx-carousel-track d-flex' },
                            style: { transition: 'transform 0.45s cubic-bezier(0.25,0.46,0.45,0.94)', 'will-change': 'transform', gap: '24px' },
                            components: [
                                {
                                    tagName: 'div',
                                    attributes: { class: 'gx-carousel-slide flex-shrink-0' },
                                    style: { width: 'calc(33.333% - 16px)' },
                                    components: [{
                                        tagName: 'div',
                                        attributes: { class: 'card border-0 shadow-sm rounded-4 overflow-hidden h-100' },
                                        components: [
                                            { type: 'image', attributes: { src: 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&q=80&w=600', class: 'card-img-top', style: 'height: 220px; object-fit: cover;' } },
                                            { tagName: 'div', attributes: { class: 'card-body p-3' }, components: [{ type: 'text', tagName: 'h6', attributes: { class: 'fw-bold mb-1' }, content: 'Urban Architecture' }, { type: 'text', tagName: 'p', attributes: { class: 'small text-secondary mb-0' }, content: 'City skylines & modern design' }] }
                                        ]
                                    }]
                                },
                                {
                                    tagName: 'div',
                                    attributes: { class: 'gx-carousel-slide flex-shrink-0' },
                                    style: { width: 'calc(33.333% - 16px)' },
                                    components: [{
                                        tagName: 'div',
                                        attributes: { class: 'card border-0 shadow-sm rounded-4 overflow-hidden h-100' },
                                        components: [
                                            { type: 'image', attributes: { src: 'https://images.unsplash.com/photo-1497215728101-856f4ea42174?auto=format&fit=crop&q=80&w=600', class: 'card-img-top', style: 'height: 220px; object-fit: cover;' } },
                                            { tagName: 'div', attributes: { class: 'card-body p-3' }, components: [{ type: 'text', tagName: 'h6', attributes: { class: 'fw-bold mb-1' }, content: 'Modern Workspace' }, { type: 'text', tagName: 'p', attributes: { class: 'small text-secondary mb-0' }, content: 'Productivity & innovation' }] }
                                        ]
                                    }]
                                },
                                {
                                    tagName: 'div',
                                    attributes: { class: 'gx-carousel-slide flex-shrink-0' },
                                    style: { width: 'calc(33.333% - 16px)' },
                                    components: [{
                                        tagName: 'div',
                                        attributes: { class: 'card border-0 shadow-sm rounded-4 overflow-hidden h-100' },
                                        components: [
                                            { type: 'image', attributes: { src: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=600', class: 'card-img-top', style: 'height: 220px; object-fit: cover;' } },
                                            { tagName: 'div', attributes: { class: 'card-body p-3' }, components: [{ type: 'text', tagName: 'h6', attributes: { class: 'fw-bold mb-1' }, content: 'Data Analytics' }, { type: 'text', tagName: 'p', attributes: { class: 'small text-secondary mb-0' }, content: 'Insights & business growth' }] }
                                        ]
                                    }]
                                },
                                {
                                    tagName: 'div',
                                    attributes: { class: 'gx-carousel-slide flex-shrink-0' },
                                    style: { width: 'calc(33.333% - 16px)' },
                                    components: [{
                                        tagName: 'div',
                                        attributes: { class: 'card border-0 shadow-sm rounded-4 overflow-hidden h-100' },
                                        components: [
                                            { type: 'image', attributes: { src: 'https://images.unsplash.com/photo-1551434678-e076c223a692?auto=format&fit=crop&q=80&w=600', class: 'card-img-top', style: 'height: 220px; object-fit: cover;' } },
                                            { tagName: 'div', attributes: { class: 'card-body p-3' }, components: [{ type: 'text', tagName: 'h6', attributes: { class: 'fw-bold mb-1' }, content: 'Team Collaboration' }, { type: 'text', tagName: 'p', attributes: { class: 'small text-secondary mb-0' }, content: 'Building together, achieving more' }] }
                                        ]
                                    }]
                                }
                            ]
                        },
                        {
                            tagName: 'button',
                            attributes: { class: 'gx-carousel-prev position-absolute top-50 start-0 translate-middle-y border-0 rounded-circle d-flex align-items-center justify-content-center shadow', style: 'width:44px;height:44px;background:rgba(255,255,255,0.95);color:#1e293b;z-index:10;cursor:pointer;' },
                            components: [{ tagName: 'i', attributes: { class: 'bi bi-chevron-left fw-bold fs-5' } }]
                        },
                        {
                            tagName: 'button',
                            attributes: { class: 'gx-carousel-next position-absolute top-50 end-0 translate-middle-y border-0 rounded-circle d-flex align-items-center justify-content-center shadow', style: 'width:44px;height:44px;background:rgba(255,255,255,0.95);color:#1e293b;z-index:10;cursor:pointer;' },
                            components: [{ tagName: 'i', attributes: { class: 'bi bi-chevron-right fw-bold fs-5' } }]
                        }
                    ]
                }
            },
            {
                id: 'testimonial-carousel-single',
                label: '<i class="bi bi-chat-quote-fill fs-4 d-block mb-1"></i>Testimoni Carousel',
                category: 'Interactive',
                content: {
                    type: 'bootstrap-testi-carousel',
                    tagName: 'div',
                    attributes: { class: 'gx-testi-carousel position-relative overflow-hidden py-4 px-1', 'data-columns': '2' },
                    style: { background: 'linear-gradient(135deg, rgba(13,110,253,0.04) 0%, rgba(13,110,253,0.01) 100%)', 'border-radius': '20px', padding: '32px 8px' },
                    components: [
                        {
                            tagName: 'div',
                            attributes: { class: 'gx-testi-track d-flex' },
                            style: { transition: 'transform 0.45s cubic-bezier(0.25,0.46,0.45,0.94)', 'will-change': 'transform', gap: '24px' },
                            components: [
                                {
                                    tagName: 'div',
                                    attributes: { class: 'gx-testi-slide flex-shrink-0' },
                                    style: { width: 'calc(50% - 12px)' },
                                    components: [{
                                        tagName: 'div',
                                        attributes: { class: 'p-5 bg-white rounded-5 shadow-sm border border-light h-100 text-center' },
                                        components: [
                                            { type: 'image', attributes: { src: 'https://i.pravatar.cc/100?u=1', class: 'rounded-circle mb-4 border border-4 border-primary border-opacity-25 shadow-sm', width: '80', height: '80' } },
                                            { tagName: 'div', attributes: { class: 'mb-3 text-warning small' }, content: '<i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>' },
                                            { type: 'text', tagName: 'p', attributes: { class: 'fs-6 text-dark fw-medium italic mb-4 lh-lg' }, content: '"Absolutely the best service we have ever experienced. The integration was seamless and the support team is outstanding."' },
                                            { type: 'text', tagName: 'h6', attributes: { class: 'fw-bold mb-1' }, content: 'Jonathan Doe' },
                                            { type: 'text', tagName: 'p', attributes: { class: 'small text-secondary mb-0' }, content: 'Director of Operations' }
                                        ]
                                    }]
                                },
                                {
                                    tagName: 'div',
                                    attributes: { class: 'gx-testi-slide flex-shrink-0' },
                                    style: { width: 'calc(50% - 12px)' },
                                    components: [{
                                        tagName: 'div',
                                        attributes: { class: 'p-5 bg-white rounded-5 shadow-sm border border-light h-100 text-center' },
                                        components: [
                                            { type: 'image', attributes: { src: 'https://i.pravatar.cc/100?u=2', class: 'rounded-circle mb-4 border border-4 border-primary border-opacity-25 shadow-sm', width: '80', height: '80' } },
                                            { tagName: 'div', attributes: { class: 'mb-3 text-warning small' }, content: '<i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>' },
                                            { type: 'text', tagName: 'p', attributes: { class: 'fs-6 text-dark fw-medium italic mb-4 lh-lg' }, content: '"The platform transformed how we manage customer feedback. Results were immediate and measurable."' },
                                            { type: 'text', tagName: 'h6', attributes: { class: 'fw-bold mb-1' }, content: 'Sarah McArthur' },
                                            { type: 'text', tagName: 'p', attributes: { class: 'small text-secondary mb-0' }, content: 'CEO @ TechFlow' }
                                        ]
                                    }]
                                },
                                {
                                    tagName: 'div',
                                    attributes: { class: 'gx-testi-slide flex-shrink-0' },
                                    style: { width: 'calc(50% - 12px)' },
                                    components: [{
                                        tagName: 'div',
                                        attributes: { class: 'p-5 bg-white rounded-5 shadow-sm border border-light h-100 text-center' },
                                        components: [
                                            { type: 'image', attributes: { src: 'https://i.pravatar.cc/100?u=4', class: 'rounded-circle mb-4 border border-4 border-primary border-opacity-25 shadow-sm', width: '80', height: '80' } },
                                            { tagName: 'div', attributes: { class: 'mb-3 text-warning small' }, content: '<i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>' },
                                            { type: 'text', tagName: 'p', attributes: { class: 'fs-6 text-dark fw-medium italic mb-4 lh-lg' }, content: '"Incredible ROI from the very first month. This is the tool every growing startup needs in their stack."' },
                                            { type: 'text', tagName: 'h6', attributes: { class: 'fw-bold mb-1' }, content: 'Marcus Lee' },
                                            { type: 'text', tagName: 'p', attributes: { class: 'small text-secondary mb-0' }, content: 'Founder @ GrowthX' }
                                        ]
                                    }]
                                }
                            ]
                        },
                        {
                            tagName: 'button',
                            attributes: { class: 'gx-testi-prev position-absolute top-50 start-0 translate-middle-y border-0 rounded-circle d-flex align-items-center justify-content-center shadow', style: 'width:44px;height:44px;background:rgba(255,255,255,0.97);color:#1e293b;z-index:10;cursor:pointer;' },
                            components: [{ tagName: 'i', attributes: { class: 'bi bi-chevron-left fw-bold fs-5' } }]
                        },
                        {
                            tagName: 'button',
                            attributes: { class: 'gx-testi-next position-absolute top-50 end-0 translate-middle-y border-0 rounded-circle d-flex align-items-center justify-content-center shadow', style: 'width:44px;height:44px;background:rgba(255,255,255,0.97);color:#1e293b;z-index:10;cursor:pointer;' },
                            components: [{ tagName: 'i', attributes: { class: 'bi bi-chevron-right fw-bold fs-5' } }]
                        }
                    ]
                }
            },
            {
                id: 'testimonial-slider-multi',
                label: '<i class="bi bi-person-heart fs-4 d-block mb-1"></i>Testimoni Slider',
                category: 'Interactive',
                content: {
                    type: 'bootstrap-testi-slider',
                    attributes: { class: 'carousel slide py-5 bg-white shadow-sm rounded-5', 'data-bs-ride': 'carousel' },
                    components: [{
                        tagName: 'div',
                        attributes: { class: 'carousel-inner text-center' },
                        components: [{
                            tagName: 'div',
                            attributes: { class: 'carousel-item active px-5' },
                            components: [
                                { type: 'image', attributes: { src: 'https://i.pravatar.cc/100?u=10', class: 'rounded-circle mb-4 border border-4 border-light shadow-sm', width: '80', height: '80' } },
                                { type: 'text', tagName: 'p', attributes: { class: 'fs-5 text-dark fw-medium px-md-5 italic mb-4' }, content: '"This tool has completely changed how we build landing pages. It is fast, intuitive, and beautiful."' },
                                { type: 'text', tagName: 'h6', attributes: { class: 'fw-bold mb-0' }, content: 'Michael Roberts' },
                                { type: 'text', tagName: 'p', attributes: { class: 'small text-secondary' }, content: 'Product Designer' }
                            ]
                        }]
                    },
                    {
                        tagName: 'button',
                        attributes: { class: 'carousel-control-prev', type: 'button', 'data-bs-slide': 'prev' },
                        components: [{ tagName: 'span', attributes: { class: 'carousel-control-prev-icon bg-dark rounded-circle', 'aria-hidden': 'true' } }]
                    },
                    {
                        tagName: 'button',
                        attributes: { class: 'carousel-control-next', type: 'button', 'data-bs-slide': 'next' },
                        components: [{ tagName: 'span', attributes: { class: 'carousel-control-next-icon bg-dark rounded-circle', 'aria-hidden': 'true' } }]
                    }]
                }
            },
            {
                id: 'team-section',
                label: '<i class="bi bi-people fs-4 d-block mb-1"></i>Team Section',
                category: 'Standard Sections',
                content: {
                    type: 'bootstrap-team-section',
                    components: [{
                        tagName: 'div',
                        attributes: { class: 'container py-5' },
                        components: [
                            {
                                tagName: 'div',
                                attributes: { class: 'text-center mb-5' },
                                components: [
                                    { type: 'text', tagName: 'span', attributes: { class: 'small text-primary fw-bold tracking-widest uppercase mb-2 d-block' }, content: '03 // ARCHITECTS' },
                                    { type: 'heading-component', tagName: 'h2', attributes: { class: 'display-5 headline font-bold tracking-tight mb-0' }, content: 'Our Leadership.' }
                                ]
                            },
                            {
                                type: 'bootstrap-team-row',
                                attributes: { class: 'row g-4' },
                                components: [
                                    {
                                        type: 'bootstrap-team-item',
                                        attributes: { class: 'col-md-3' },
                                        components: [{
                                            tagName: 'div',
                                            attributes: { class: 'gx-team-card text-center' },
                                            components: [
                                                { 
                                                  tagName: 'div', 
                                                  attributes: { class: 'ratio ratio-3x4 mb-4 overflow-hidden border border-light bg-secondary bg-opacity-10' }, 
                                                  components: [{ 
                                                    type: 'image', 
                                                    attributes: { 
                                                        src: 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400&q=80', 
                                                        class: 'w-100 h-100 object-cover grayscale transition-700 position-absolute top-0 start-0' 
                                                    } 
                                                  }] 
                                                },
                                                { type: 'text', tagName: 'h4', attributes: { class: 'h5 fw-bold mb-1' }, content: 'Marcus Chen' },
                                                { type: 'text', tagName: 'p', attributes: { class: 'small text-primary fw-bold text-uppercase tracking-widest mb-0' }, content: 'Chief Executive Architect' }
                                            ]
                                        }]
                                    },
                                    {
                                        type: 'bootstrap-team-item',
                                        attributes: { class: 'col-md-3' },
                                        components: [{
                                            tagName: 'div',
                                            attributes: { class: 'gx-team-card text-center' },
                                            components: [
                                                { 
                                                  tagName: 'div', 
                                                  attributes: { class: 'ratio ratio-3x4 mb-4 overflow-hidden border border-light bg-secondary bg-opacity-10' }, 
                                                  components: [{ 
                                                    type: 'image', 
                                                    attributes: { 
                                                        src: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&q=80', 
                                                        class: 'w-100 h-100 object-cover grayscale transition-700 position-absolute top-0 start-0' 
                                                    } 
                                                  }] 
                                                },
                                                { type: 'text', tagName: 'h4', attributes: { class: 'h5 fw-bold mb-1' }, content: 'Elena Rodriguez' },
                                                { type: 'text', tagName: 'p', attributes: { class: 'small text-primary fw-bold text-uppercase tracking-widest mb-0' }, content: 'VP of Engineering' }
                                            ]
                                        }]
                                    },
                                    {
                                        type: 'bootstrap-team-item',
                                        attributes: { class: 'col-md-3' },
                                        components: [{
                                            tagName: 'div',
                                            attributes: { class: 'gx-team-card text-center' },
                                            components: [
                                                { 
                                                  tagName: 'div', 
                                                  attributes: { class: 'ratio ratio-3x4 mb-4 overflow-hidden border border-light bg-secondary bg-opacity-10' }, 
                                                  components: [{ 
                                                    type: 'image', 
                                                    attributes: { 
                                                        src: 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=400&q=80', 
                                                        class: 'w-100 h-100 object-cover grayscale transition-700 position-absolute top-0 start-0' 
                                                    } 
                                                  }] 
                                                },
                                                { type: 'text', tagName: 'h4', attributes: { class: 'h5 fw-bold mb-1' }, content: 'David Kessler' },
                                                { type: 'text', tagName: 'p', attributes: { class: 'small text-primary fw-bold text-uppercase tracking-widest mb-0' }, content: 'Operations Director' }
                                            ]
                                        }]
                                    },
                                    {
                                        type: 'bootstrap-team-item',
                                        attributes: { class: 'col-md-3' },
                                        components: [{
                                            tagName: 'div',
                                            attributes: { class: 'gx-team-card text-center' },
                                            components: [
                                                { 
                                                  tagName: 'div', 
                                                  attributes: { class: 'ratio ratio-3x4 mb-4 overflow-hidden border border-light bg-secondary bg-opacity-10' }, 
                                                  components: [{ 
                                                    type: 'image', 
                                                    attributes: { 
                                                        src: 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=400&q=80', 
                                                        class: 'w-100 h-100 object-cover grayscale transition-700 position-absolute top-0 start-0' 
                                                    } 
                                                  }] 
                                                },
                                                { type: 'text', tagName: 'h4', attributes: { class: 'h5 fw-bold mb-1' }, content: 'Sarah Jenkins' },
                                                { type: 'text', tagName: 'p', attributes: { class: 'small text-primary fw-bold text-uppercase tracking-widest mb-0' }, content: 'Head of Experience' }
                                            ]
                                        }]
                                    }
                                ]
                            }
                        ]
                    }]
                }
            },
            {
                id: 'counter-stat-item',
                label: '<i class="bi bi-clock-history fs-4 d-block mb-1"></i>Counter Widget',
                category: 'Interactive',
                content: {
                    type: 'bootstrap-counter-row',
                    attributes: { class: 'row g-4 py-5 text-center' },
                    components: [
                        {
                            type: 'bootstrap-counter-item',
                            attributes: { class: 'col-md-3' },
                            components: [{
                                tagName: 'div',
                                attributes: { class: 'p-4 bg-white rounded-4 shadow-sm border border-light h-100' },
                                components: [
                                    { tagName: 'i', attributes: { class: 'bi bi-people-fill text-primary display-5 mb-3 d-block' } },
                                    { type: 'text', tagName: 'h2', attributes: { class: 'fw-bold mb-1', 'data-target': '1000' }, content: '1000' },
                                    { type: 'text', tagName: 'p', attributes: { class: 'small text-uppercase text-secondary fw-bold tracking-widest mb-0' }, content: 'Happy Clients' }
                                ]
                            }]
                        },
                        {
                            type: 'bootstrap-counter-item',
                            attributes: { class: 'col-md-3' },
                            components: [{
                                tagName: 'div',
                                attributes: { class: 'p-4 bg-white rounded-4 shadow-sm border border-light h-100' },
                                components: [
                                    { tagName: 'i', attributes: { class: 'bi bi-trophy-fill text-warning display-5 mb-3 d-block' } },
                                    { type: 'text', tagName: 'h2', attributes: { class: 'fw-bold mb-1', 'data-target': '150' }, content: '150' },
                                    { type: 'text', tagName: 'p', attributes: { class: 'small text-uppercase text-secondary fw-bold tracking-widest mb-0' }, content: 'Awards Won' }
                                ]
                            }]
                        },
                        {
                            type: 'bootstrap-counter-item',
                            attributes: { class: 'col-md-3' },
                            components: [{
                                tagName: 'div',
                                attributes: { class: 'p-4 bg-white rounded-4 shadow-sm border border-light h-100' },
                                components: [
                                    { tagName: 'i', attributes: { class: 'bi bi-check-circle-fill text-success display-5 mb-3 d-block' } },
                                    { type: 'text', tagName: 'h2', attributes: { class: 'fw-bold mb-1', 'data-target': '2000' }, content: '2000' },
                                    { type: 'text', tagName: 'p', attributes: { class: 'small text-uppercase text-secondary fw-bold tracking-widest mb-0' }, content: 'Projects Done' }
                                ]
                            }]
                        },
                        {
                            type: 'bootstrap-counter-item',
                            attributes: { class: 'col-md-3' },
                            components: [{
                                tagName: 'div',
                                attributes: { class: 'p-4 bg-white rounded-4 shadow-sm border border-light h-100' },
                                components: [
                                    { tagName: 'i', attributes: { class: 'bi bi-lightning-charge-fill text-info display-5 mb-3 d-block' } },
                                    { type: 'text', tagName: 'h2', attributes: { class: 'fw-bold mb-1', 'data-target': '24' }, content: '24' },
                                    { type: 'text', tagName: 'p', attributes: { class: 'small text-uppercase text-secondary fw-bold tracking-widest mb-0' }, content: 'Support Hours' }
                                ]
                            }]
                        }
                    ]
                }
            },
            {
                id: 'recent-posts-widget',
                label: '<i class="bi bi-clock-history fs-4 d-block mb-1"></i>Recent Posts',
                category: 'CMS Dynamic',
                content: { type: 'recent-posts-dynamic' }
            },
            {
                id: 'random-posts-widget',
                label: '<i class="bi bi-shuffle fs-4 d-block mb-1"></i>Random Posts',
                category: 'CMS Dynamic',
                content: { type: 'random-posts-dynamic' }
            },
            {
                id: 'custom-posts-widget',
                label: '<i class="bi bi-pin-angle fs-4 d-block mb-1"></i>Custom Posts',
                category: 'CMS Dynamic',
                content: { type: 'custom-posts-dynamic' }
            },
            {
                id: 'posts-by-category-widget',
                label: '<i class="bi bi-folder2-open fs-4 d-block mb-1"></i>Post by Category',
                category: 'CMS Dynamic',
                content: { type: 'posts-by-category-dynamic' }
            },
            {
                id: 'posts-by-type-widget',
                label: '<i class="bi bi-files fs-4 d-block mb-1"></i>Post by Type',
                category: 'CMS Dynamic',
                content: { type: 'posts-by-type-dynamic' }
            },
            {
                id: 'posts-by-author-widget',
                label: '<i class="bi bi-person-badge fs-4 d-block mb-1"></i>Post by Author',
                category: 'CMS Dynamic',
                content: { type: 'posts-by-author-dynamic' }
            },
            {
                id: 'html-code-widget',
                label: '<i class="bi bi-code-slash fs-4 d-block mb-1"></i>Raw HTML',
                category: 'Basics',
                content: { type: 'gx-custom-code' }
            }
        ];
    }

    function addBlocks(editor) {
        const bm = editor.BlockManager;

        // Custom Blocks from hook
        let blocks = Array.isArray(window.dynamicBuilderBlocks) ? window.dynamicBuilderBlocks : [];
        if (blocks.length === 0) {
            blocks = getPremiumBlocks();
        }

        blocks.forEach(function (block) {
            const blockConfig = Object.assign({ category: 'General' }, block);
            const blockId = blockConfig.id || ('block-' + Math.random().toString(36).substr(2, 9));
            bm.add(blockId, blockConfig);
        });
    }

        function registerDynamicComponents(editor) {
            // Global Component Behavior Enhancements
            // Standardize Global Behavior
            editor.DomComponents.addType('default', {
                model: {
                    defaults: {
                        draggable: true,
                        droppable: true,
                    }
                }
            });

            // Force section and containers to be movable and droppable
            const wrapperTypes = ['section', 'header', 'footer', 'div'];
            wrapperTypes.forEach(type => {
                editor.DomComponents.addType(type, {
                    model: {
                        defaults: {
                            draggable: true,
                            droppable: true,
                        }
                    }
                });
            });

            // ============================================================
            // CRITICAL FIX: Override 'row' and 'column' to use <div>
            // GrapesJS defaults 'row' to <tr> and 'column' to <td>.
            // When the HTML is serialized and reloaded, the browser's HTML
            // parser strips orphan <tr> tags (outside a <table>), causing
            // the entire .row wrapper to disappear and columns to lose
            // their Bootstrap grid context — resulting in the "shift left" bug.
            // ============================================================
            editor.DomComponents.addType('row', {
                model: {
                    defaults: {
                        tagName: 'div',
                        draggable: true,
                        droppable: true,
                        attributes: { class: 'row' },
                    }
                }
            });

            editor.DomComponents.addType('column', {
                model: {
                    defaults: {
                        tagName: 'div',
                        draggable: true,
                        droppable: true,
                        attributes: { class: 'col' },
                    }
                }
            });

            // Recent Posts Component
            editor.DomComponents.addType('recent-posts-dynamic', {
                model: {
                    defaults: {
                        tagName: 'div',
                        attributes: { 
                            class: 'recent-posts-container',
                            'data-count': '3'
                        },
                        style: { padding: '15px' },
                        traits: [
                            {
                                type: 'number',
                                label: 'Posts Count',
                                name: 'data-count',
                                min: 1,
                                max: 12,
                            }
                        ],
                        content: '<div class="text-center p-5 bg-light rounded-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-3 text-muted fw-bold">Connecting to Library...</p></div>'
                    }
                },
                view: {
                    init() {
                        this.listenTo(this.model, 'change:attributes:data-count', this.onUpdate);
                        setTimeout(() => { this.onUpdate(); }, 200);
                    },
                    onUpdate() {
                        const count = this.model.getAttributes()['data-count'] || 3;
                        const el = this.el;
                        if (el) {
                            // Always force re-load if we are explicitly calling onUpdate
                            el.innerHTML = '<div class="text-center p-5 bg-light rounded-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-3 text-muted fw-bold">Syncing Library Grid...</p></div>';
                            el.setAttribute('data-loaded', 'false');
                            
                            const loader = window.loadDynamicBuilderContent || (window.parent && window.parent.loadDynamicBuilderContent);
                            if (loader) {
                                loader(el, { action: 'recent_posts', count: count });
                            } else {
                                console.warn('[Builder] Content loader function not found.');
                            }
                        }
                    }
                }
            });

            // Random Posts Component
            editor.DomComponents.addType('random-posts-dynamic', {
                model: {
                    defaults: {
                        tagName: 'div',
                        attributes: { 
                            class: 'recent-posts-container',
                            'data-count': '3'
                        },
                        style: { padding: '15px' },
                        traits: [
                            {
                                type: 'number',
                                label: 'Posts Count',
                                name: 'data-count',
                                min: 1,
                                max: 12,
                            }
                        ],
                        content: '<div class="text-center p-5 bg-light rounded-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-3 text-muted fw-bold">Connecting to Library...</p></div>'
                    }
                },
                view: {
                    init() {
                        this.listenTo(this.model, 'change:attributes:data-count', this.onUpdate);
                        setTimeout(() => { this.onUpdate(); }, 200);
                    },
                    onUpdate() {
                        const count = this.model.getAttributes()['data-count'] || 3;
                        const el = this.el;
                        if (el) {
                            el.innerHTML = '<div class="text-center p-5 bg-light rounded-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-3 text-muted fw-bold">Syncing Random Posts...</p></div>';
                            el.setAttribute('data-loaded', 'false');
                            const loader = window.loadDynamicBuilderContent || (window.parent && window.parent.loadDynamicBuilderContent);
                            if (loader) loader(el, { action: 'random_posts', count: count });
                        }
                    }
                }
            });

            // Custom Posts Component
            editor.DomComponents.addType('custom-posts-dynamic', {
                model: {
                    defaults: {
                        tagName: 'div',
                        attributes: { 
                            class: 'recent-posts-container',
                            'data-count': '3',
                            'data-ids': ''
                        },
                        style: { padding: '15px' },
                        traits: [
                             {
                                type: 'text',
                                label: 'Post IDs (csv)',
                                name: 'data-ids',
                                placeholder: 'e.g. 10,12,15'
                            },
                            {
                                type: 'number',
                                label: 'Limit Count',
                                name: 'data-count',
                                min: 1,
                                max: 12,
                            }
                        ],
                        content: '<div class="text-center p-5 bg-light rounded-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-3 text-muted fw-bold">Connecting to Library...</p></div>'
                    }
                },
                view: {
                    init() {
                        this.listenTo(this.model, 'change:attributes:data-count change:attributes:data-ids', this.onUpdate);
                        setTimeout(() => { this.onUpdate(); }, 200);
                    },
                    onUpdate() {
                        const attrs = this.model.getAttributes();
                        const count = attrs['data-count'] || 3;
                        const ids = attrs['data-ids'] || '';
                        const el = this.el;
                        if (el) {
                            el.innerHTML = '<div class="text-center p-5 bg-light rounded-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-3 text-muted fw-bold">Syncing Custom Selection...</p></div>';
                            el.setAttribute('data-loaded', 'false');
                            const loader = window.loadDynamicBuilderContent || (window.parent && window.parent.loadDynamicBuilderContent);
                            if (loader) loader(el, { action: 'custom_posts', count: count, ids: ids });
                        }
                    }
                }
            });

            // Post by Category Component
            editor.DomComponents.addType('posts-by-category-dynamic', {
                model: {
                    defaults: {
                        tagName: 'div',
                        attributes: { class: 'recent-posts-container', 'data-count': '3', 'data-category': '' },
                        style: { padding: '15px' },
                        traits: [
                            { type: 'text', label: 'Category ID/Slug', name: 'data-category' },
                            { type: 'number', label: 'Posts Count', name: 'data-count', min: 1, max: 12 }
                        ],
                        content: '<div class="text-center p-5 bg-light rounded-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-3 text-muted fw-bold">Connecting to Library...</p></div>'
                    }
                },
                view: {
                    init() {
                        this.listenTo(this.model, 'change:attributes:data-count change:attributes:data-category', this.onUpdate);
                        setTimeout(() => { this.onUpdate(); }, 200);
                    },
                    onUpdate() {
                        const attrs = this.model.getAttributes();
                        const el = this.el;
                        if (el) {
                            el.innerHTML = '<div class="text-center p-5 bg-light rounded-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-3 text-muted fw-bold">Syncing Category Posts...</p></div>';
                            el.setAttribute('data-loaded', 'false');
                            const loader = window.loadDynamicBuilderContent || (window.parent && window.parent.loadDynamicBuilderContent);
                            if (loader) loader(el, { action: 'posts_by_category', count: attrs['data-count'], category: attrs['data-category'] });
                        }
                    }
                }
            });

            // Post by Type Component
            editor.DomComponents.addType('posts-by-type-dynamic', {
                model: {
                    defaults: {
                        tagName: 'div',
                        attributes: { class: 'recent-posts-container', 'data-count': '3', 'data-type': 'post' },
                        style: { padding: '15px' },
                        traits: [
                            { 
                                type: 'select', 
                                label: 'Post Type', 
                                name: 'data-type',
                                options: [
                                    { value: 'post', name: 'Posts' },
                                    { value: 'page', name: 'Pages' }
                                ]
                            },
                            { type: 'number', label: 'Posts Count', name: 'data-count', min: 1, max: 12 }
                        ],
                        content: '<div class="text-center p-5 bg-light rounded-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-3 text-muted fw-bold">Connecting to Library...</p></div>'
                    }
                },
                view: {
                    init() {
                        this.listenTo(this.model, 'change:attributes:data-count change:attributes:data-type', this.onUpdate);
                        setTimeout(() => { this.onUpdate(); }, 200);
                    },
                    onUpdate() {
                        const attrs = this.model.getAttributes();
                        const el = this.el;
                        if (el) {
                            el.innerHTML = '<div class="text-center p-5 bg-light rounded-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-3 text-muted fw-bold">Syncing Type Selection...</p></div>';
                            el.setAttribute('data-loaded', 'false');
                            const loader = window.loadDynamicBuilderContent || (window.parent && window.parent.loadDynamicBuilderContent);
                            if (loader) loader(el, { action: 'posts_by_type', count: attrs['data-count'], type: attrs['data-type'] });
                        }
                    }
                }
            });

            // Post by Author Component
            editor.DomComponents.addType('posts-by-author-dynamic', {
                model: {
                    defaults: {
                        tagName: 'div',
                        attributes: { class: 'recent-posts-container', 'data-count': '3', 'data-author': '' },
                        style: { padding: '15px' },
                        traits: [
                            { type: 'text', label: 'Author ID/User', name: 'data-author' },
                            { type: 'number', label: 'Posts Count', name: 'data-count', min: 1, max: 12 }
                        ],
                        content: '<div class="text-center p-5 bg-light rounded-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-3 text-muted fw-bold">Connecting to Library...</p></div>'
                    }
                },
                view: {
                    init() {
                        this.listenTo(this.model, 'change:attributes:data-count change:attributes:data-author', this.onUpdate);
                        setTimeout(() => { this.onUpdate(); }, 200);
                    },
                    onUpdate() {
                        const attrs = this.model.getAttributes();
                        const el = this.el;
                        if (el) {
                            el.innerHTML = '<div class="text-center p-5 bg-light rounded-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-3 text-muted fw-bold">Syncing Author Feed...</p></div>';
                            el.setAttribute('data-loaded', 'false');
                            const loader = window.loadDynamicBuilderContent || (window.parent && window.parent.loadDynamicBuilderContent);
                            if (loader) loader(el, { action: 'posts_by_author', count: attrs['data-count'], author: attrs['data-author'] });
                        }
                    }
                }
            });

            // Bootstrap Progress Bar System
            editor.DomComponents.addType('bootstrap-progress-container', {
                model: {
                    defaults: {
                        tagName: 'div',
                        attributes: { class: 'mb-4 progress-wrapper' },
                        style: { padding: '15px' },
                        droppable: 'bootstrap-progress-item, label, p, span, h1, h2, h3, h4, h5, h6'
                    },
                    init() {
                        const toolbar = this.get('toolbar');
                        toolbar.unshift({
                            attributes: { class: 'fa fa-plus-square', title: 'Add New Bar' },
                            command: 'add-progress-item',
                        });
                        this.set('toolbar', toolbar);
                    }
                }
            });

            editor.DomComponents.addType('bootstrap-progress-item', {
                model: {
                    defaults: {
                        tagName: 'div',
                        attributes: { class: 'progress mb-3', style: 'height: 20px;' },
                        value: 75,
                        color: 'bg-primary',
                        striped: false,
                        traits: [
                            {
                                type: 'number',
                                label: 'Percentage',
                                name: 'value',
                                min: 0,
                                max: 100,
                                changeProp: 1
                            },
                            {
                                type: 'select',
                                label: 'Color',
                                name: 'color',
                                changeProp: 1,
                                options: [
                                    { value: 'bg-primary', name: 'Primary (Blue)' },
                                    { value: 'bg-success', name: 'Success (Green)' },
                                    { value: 'bg-info', name: 'Info (Cyan)' },
                                    { value: 'bg-warning', name: 'Warning (Yellow)' },
                                    { value: 'bg-danger', name: 'Danger (Red)' },
                                    { value: 'bg-dark', name: 'Dark' }
                                ]
                            },
                            {
                                type: 'checkbox',
                                label: 'Striped',
                                name: 'striped',
                                changeProp: 1
                            }
                        ],
                        components: [
                            {
                                tagName: 'div',
                                attributes: { 
                                    class: 'progress-bar progress-bar-animated', 
                                    role: 'progressbar',
                                    style: 'width: 75%'
                                },
                                content: '75%'
                            }
                        ]
                    },
                    init() {
                        this.listenTo(this, 'change:value change:color change:striped', this.handleUpdate);
                        // Trigger initial update to sync attributes
                        setTimeout(() => this.handleUpdate(), 100);
                    },
                    handleUpdate() {
                        const val = this.get('value');
                        const color = this.get('color');
                        const striped = this.get('striped');
                        
                        const bar = this.get('components').at(0);
                        if (bar) {
                            // Update width and text
                            bar.addStyle({ width: val + '%' });
                            bar.set('content', val + '%');
                            
                            // Update classes
                            let classes = ['progress-bar', 'progress-bar-animated', color];
                            if (striped) classes.push('progress-bar-striped');
                            bar.setAttributes({ class: classes.join(' ') });
                        }
                        
                        if (this.view) this.view.render();
                    }
                }
            });

            // Command to add a new progress item
            editor.Commands.add('add-progress-item', {
                run(editor, sender) {
                    const selected = editor.getSelected();
                    if (!selected) return;
                    
                    const container = selected.closest('[data-gjs-type="bootstrap-progress-container"]');
                    if (!container) return;

                    container.append([
                        { tagName: 'label', attributes: { class: 'form-label fw-bold small mb-2' }, content: 'New Skill' },
                        { type: 'bootstrap-progress-item' }
                    ]);
                }
            });

            // Dynamic Heading Component
            editor.DomComponents.addType('heading-component', {
                extend: 'text',
                model: {
                    defaults: {
                        tagName: 'h2',
                        classes: ['fw-bold'],
                        style: { },
                        content: 'Section Title Here',
                        traits: [
                            {
                                type: 'select',
                                label: 'Heading Type',
                                name: 'tagName',
                                changeProp: 1,
                                options: [
                                    { value: 'h1', name: 'H1' },
                                    { value: 'h2', name: 'H2' },
                                    { value: 'h3', name: 'H3' },
                                    { value: 'h4', name: 'H4' },
                                    { value: 'h5', name: 'H5' },
                                    { value: 'h6', name: 'H6' }
                                ]
                            }
                        ]
                    },
                    init() {
                        this.listenTo(this, 'change:tagName', this.handleTagChange);
                    },
                    handleTagChange() {
                        console.log('[Builder] Heading tag changed to:', this.get('tagName'));
                        if (this.view) {
                            this.view.render();
                            editor.select(this);
                        }
                    }
                },
                view: {
                    tagName() {
                        return this.model.get('tagName') || 'h2';
                    }
                }
            });

            // Bootstrap Icon Component
            editor.DomComponents.addType('bs-icon-component', {
                model: {
                    defaults: {
                        tagName: 'i',
                        attributes: { 
                            class: 'bi bi-rocket-takeoff-fill fs-2 text-danger'
                        },
                        style: { padding: '15px' },
                        traits: [
                            {
                                type: 'text',
                                label: 'Icon Class',
                                name: 'data-icon',
                                placeholder: 'bi-star, bi-heart',
                                value: 'bi-rocket-takeoff-fill'
                            },
                            {
                                type: 'select',
                                label: 'Size',
                                name: 'data-size',
                                options: [
                                    { value: 'fs-1', name: 'XL' },
                                    { value: 'fs-2', name: 'Large' },
                                    { value: 'fs-3', name: 'Medium' },
                                    { value: 'fs-4', name: 'Small' },
                                    { value: 'fs-5', name: 'XS' },
                                    { value: 'fs-6', name: 'XXS' }
                                ]
                            },
                            {
                                type: 'select',
                                label: 'Color',
                                name: 'data-color',
                                options: [
                                    { value: 'text-primary', name: 'Primary' },
                                    { value: 'text-secondary', name: 'Secondary' },
                                    { value: 'text-success', name: 'Success' },
                                    { value: 'text-danger', name: 'Danger' },
                                    { value: 'text-warning', name: 'Warning' },
                                    { value: 'text-info', name: 'Info' },
                                    { value: 'text-light', name: 'Light' },
                                    { value: 'text-dark', name: 'Dark' },
                                    { value: 'text-white', name: 'White' }
                                ]
                            }
                        ]
                    }
                },
                view: {
                    init() {
                        this.listenTo(this.model, 'change:attributes:data-icon change:attributes:data-size change:attributes:data-color', this.onUpdate);
                    },
                    onUpdate() {
                        const attrs = this.model.getAttributes();
                        const icon = attrs['data-icon'] || 'bi-rocket-takeoff-fill';
                        const size = attrs['data-size'] || 'fs-2';
                        const color = attrs['data-color'] || 'text-danger';
                        
                        // Clean up existing classes and rebuild
                        const el = this.el;
                        el.className = `bi ${icon} ${size} ${color}`;
                    }
                }
            });

            // ---- Bootstrap Carousel Component Type Registration ----
            // This ensures GrapesJS recognizes the carousel when reloading saved HTML.
            editor.DomComponents.addType('bootstrap-carousel', {
                isComponent: el => el.classList && el.classList.contains('carousel') && el.classList.contains('slide'),
                model: {
                    defaults: {
                        name: 'Image Slider',
                        droppable: true,
                        draggable: true,
                    }
                }
            });

            editor.DomComponents.addType('bootstrap-image-carousel', {
                isComponent: el => el.classList && el.classList.contains('gx-img-carousel'),
                model: {
                    defaults: {
                        name: 'Image Carousel',
                        droppable: true,
                        draggable: true,
                    }
                }
            });

            editor.DomComponents.addType('bootstrap-testi-carousel', {
                isComponent: el => el.classList && el.classList.contains('gx-testi-carousel'),
                model: {
                    defaults: {
                        name: 'Testimonial Carousel',
                        droppable: true,
                        draggable: true,
                    }
                }
            });

            // 0. Force Vertical Layout via Global CSS Injection (Targeting Exact HTML Structure)
            if (!document.getElementById('gx-builder-custom-css')) {
                const style = document.createElement('style');
                style.id = 'gx-builder-custom-css';
                style.innerHTML = `
                    .gjs-trt-trait__wrp { display: block !important; padding: 10px 5px !important; }
                    .gjs-trt-trait { display: block !important; width: 100% !important; }
                    .gjs-label-wrp { display: block !important; width: 100% !important; margin-bottom: 8px !important; }
                    .gjs-label { display: block !important; width: 100% !important; font-weight: bold !important; color: #ccc !important; text-align: left !important; }
                    .gjs-field-wrp { display: block !important; width: 100% !important; }
                    .gjs-field-textarea { min-height: 250px !important; width: 100% !important; border-radius: 4px !important; }
                `;
                document.head.appendChild(style);
            }

            // Custom Trait Type for Raw HTML
            editor.TraitManager.addType('gx-code-textarea', {
                noLabel: false,
                event: 'keyup change', 
                getInputEl() {
                    if (!this.inputEl) {
                        this.inputEl = document.createElement('textarea');
                        this.inputEl.classList.add('gjs-field', 'gjs-field-textarea');
                        this.inputEl.setAttribute('placeholder', 'Paste your code here...');
                        this.inputEl.style.width = '100%';
                        this.inputEl.style.minHeight = '250px';
                        this.inputEl.style.display = 'block';
                    }
                    return this.inputEl;
                },
                onRender() {
                    // CSS Injection handles this now based on exact HTML structure
                },
                onEvent({ elInput, component }) {
                    const value = elInput.value || '';
                    component.set('data-gx-code', value);
                },
                onUpdate({ elInput, component }) {
                    // FORCE SYNC: Read directly from attribute if model is empty
                    let val = component.get('data-gx-code') || component.getAttributes()['data-gx-code'] || '';
                    let display = val;
                    if (val.indexOf('base64:') === 0) {
                        try { display = btou(val.substring(7)); } catch(e) {}
                    }
                    if (elInput.value !== display) {
                        elInput.value = display;
                    }
                }
            });

            // Custom Code Component for Raw HTML/JS/CSS
            editor.DomComponents.addType('gx-custom-code', {
                isComponent: el => (el.getAttribute && el.getAttribute('class') && el.getAttribute('class').includes('custom-code-container')),
                model: {
                    defaults: {
                        name: 'Raw HTML (Code)',
                        tagName: 'div',
                        attributes: { class: 'custom-code-container', 'data-gjs-type': 'gx-custom-code', 'data-gx-code': '' },
                        droppable: false,
                        editable: false,
                        code: '', // Internal property for transparency
                        content: '<div class="p-4 bg-dark text-white rounded-3 text-center" style="border: 3px dashed rgba(255,255,255,0.4); cursor:pointer;"><i class="bi bi-code-square fs-1 d-block mb-2 text-warning"></i><span class="small fw-bold">RAW HTML CONTENT</span><br><span class="opacity-50 small">Click & open Settings Panel (Gear Icon) to edit code</span></div>',
                        traits: [
                            {
                                type: 'gx-code-textarea',
                                label: 'HTML/JS/CSS Code',
                                name: 'data-gx-code',  // Unique name
                                placeholder: 'Paste your HTML, <style> or <script> tags here...'
                            }
                        ],
                    },
                    init() {
                    },
                    updated(property, value, prev) {
                        if (property === 'data-gx-code') {
                            const code = value || '';
                            if (code && code.indexOf('base64:') !== 0) {
                                const b64 = 'base64:' + utob(code);
                                this.addAttributes({ 'data-gx-code': b64 });
                            }
                            this.renderContent();
                        }
                    },
                    renderContent() {
                        const code = this.get('data-gx-code') || '';
                        let displayCode = code;
                        if (code.indexOf('base64:') === 0) {
                            try { displayCode = btou(code.substring(7)); } catch(e) {}
                        }

                        let preview = (displayCode || '').substring(0, 100).replace(/</g, '&lt;').replace(/>/g, '&gt;');
                        if (preview.length >= 100) preview += '...';
                        
                        this.set('content', `
                            <div class="p-4 bg-dark text-white rounded-3 text-center" style="border: 3px dashed rgba(255,255,255,0.4); cursor:pointer;">
                                <i class="bi bi-code-square fs-1 d-block mb-2 text-warning"></i>
                                <span class="small fw-bold">RAW HTML CONTENT</span><br>
                                <div class="mt-2 opacity-50 extra-small text-truncate" style="font-family: monospace; max-width: 200px; margin: 0 auto;">${preview || 'Click to edit code'}</div>
                            </div>
                        `);
                    }
                },
                view: {
                    init() {
                        this.listenTo(this.model, 'change:content', this.render);
                    }
                }
            });

            // Global fallback watcher
            editor.on('component:update:data-gx-code', (model) => {
                const code = model.get('data-gx-code');
                if (code && code.indexOf('base64:') !== 0) {
                    model.addAttributes({ 'data-gx-code': 'base64:' + utob(code) });
                }
            });

            // Comparison Table Component with Add Column feature
            editor.DomComponents.addType('comparison-table', {
                model: {
                    defaults: {
                        name: 'Comparison Table',
                        droppable: true,
                        draggable: true,
                        attributes: { class: 'comparison-table-wrapper' }
                    },
                    init() {
                        const toolbar = this.get('toolbar');
                        toolbar.unshift({
                            attributes: { class: 'fa fa-plus-square', title: 'Add New Column' },
                            command: 'add-table-column',
                        });
                        this.set('toolbar', toolbar);
                    }
                }
            });

            // Command to add a new table column
            editor.Commands.add('add-table-column', {
                run(editor) {
                    const selected = editor.getSelected();
                    if (!selected) return;

                    const tableWrapper = selected.closest('[data-gjs-type="comparison-table"]');
                    if (!tableWrapper) return;

                    const table = tableWrapper.view.el.querySelector('table');
                    if (!table) return;

                    // Add to Header
                    const headerRow = table.querySelector('thead tr');
                    if (headerRow) {
                        const th = document.createElement('th');
                        th.className = 'py-4 text-center border-0 text-dark';
                        th.style.width = '200px';
                        th.setAttribute('data-gjs-type', 'text');
                        th.innerHTML = 'New Tier';
                        headerRow.appendChild(th);
                    }

                    // Add to Body rows
                    const bodyRows = table.querySelectorAll('tbody tr');
                    bodyRows.forEach(row => {
                        const td = document.createElement('td');
                        td.className = 'text-center';
                        td.setAttribute('data-gjs-type', 'default');
                        td.innerHTML = '<i class="bi bi-check-circle-fill text-success fs-5"></i>';
                        row.appendChild(td);
                    });

                    // Update GrapesJS model through components for better editability
                    const html = tableWrapper.view.el.innerHTML;
                    tableWrapper.set('content', ''); // Clear
                    tableWrapper.append(html); 
                    
                    editor.select(null);
                    setTimeout(() => editor.select(tableWrapper), 10);
                }
            });

            // Content Card System Component
            editor.DomComponents.addType('content-card-system', {
                isComponent: el => (el.getAttribute && el.getAttribute('class') && el.getAttribute('class').includes('content-card-wrapper')),
                model: {
                    defaults: {
                        name: 'Content Card',
                        draggable: true,
                        droppable: true,
                    }
                }
            });
    }

    function renderCards(container, data, limit) {
        // Default mock data if CMS is empty or API fails
        const mockData = [
            {
                title: "The Future of Cloud Computing in 2026",
                excerpt: "Exploring the next frontier of decentralized cloud infrastructure and its impact on global enterprises.",
                category: "Technology",
                date: "Apr 07, 2026",
                url: "#",
                image: "https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&q=80&w=800"
            },
            {
                title: "Mastering Hybrid Infrastructure Architecture",
                excerpt: "A comprehensive guide to building resilient hybrid systems for mission-critical digital environments.",
                category: "Infrastructure",
                date: "Apr 05, 2026",
                url: "#",
                image: "https://images.unsplash.com/photo-1558494949-ef010cbdcc51?auto=format&fit=crop&q=80&w=800"
            },
            {
                title: "Cyber Security: The Artificial Intelligence Shift",
                excerpt: "How AI-driven threat detection is revolutionizing security protocols for the modern digital landscape.",
                category: "Cyber Security",
                date: "Apr 02, 2026",
                url: "#",
                image: "https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&q=80&w=800"
            },
            {
                title: "Digital Transformation Strategies for 2026",
                excerpt: "Adopting a cloud-native mindset to accelerate business growth and innovation.",
                category: "Strategy",
                date: "Mar 30, 2026",
                url: "#",
                image: "https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=800"
            },
            {
                title: "The Rise of Edge Computing in Industrial IoT",
                excerpt: "Processing data closer to the source for real-time insights and operational efficiency.",
                category: "IoT",
                date: "Mar 25, 2026",
                url: "#",
                image: "https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&q=80&w=800"
            },
            {
                title: "Building Resilient Digital Ecosystems",
                excerpt: "Fostering collaboration and innovation in a connected world through robust digital platforms.",
                category: "Ecosystem",
                date: "Mar 20, 2026",
                url: "#",
                image: "https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&q=80&w=800"
            }
        ];

        let finalData = (data && data.length > 0) ? data : mockData;
        
        // Apply limit if specified
        if (limit) {
            finalData = finalData.slice(0, parseInt(limit));
        }

        let html = '<div class="row g-4 text-dark text-start gjs-no-pointer">';
        finalData.forEach(function (post) {
            html += '<div class="col-md-4">';
            html += '<div class="card border-0 bg-white shadow-sm h-100 rounded-5 overflow-hidden transition-all" style="transition: transform 0.3s ease;">';
            html += '<div class="ratio ratio-16x9">';
            html += '<img src="' + post.image + '" class="object-fit-cover w-100 h-100" alt="' + post.title + '">';
            html += '</div>';
            html += '<div class="card-body p-4">';
            html += '<div class="d-flex justify-content-between align-items-center mb-3">';
            html += '<span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill" style="font-size: 11px; font-weight: 700;">' + (post.category || 'General').toUpperCase() + '</span>';
            html += '<small class="text-muted" style="font-size: 11px;"><i class="bi bi-calendar3 me-2"></i>' + post.date + '</small>';
            html += '</div>';
            html += '<h5 class="fw-bold mb-3 lh-base" style="font-size: 1.15rem;">' + post.title + '</h5>';
            html += '<p class="text-secondary small mb-0 opacity-75 lh-lg">' + (post.excerpt || '') + '</p>';
            html += '</div></div></div>';
        });
        html += '</div>';
        
        if (container) {
            container.innerHTML = html;
            container.setAttribute('data-loaded', 'true');
        }
    }

    function getApiUrl(options) {
        let url = window.dynamicBuilderConfig.apiEndpoint;
        const limit = options.count || 3;
        const action = options.action || 'recent_posts';
        const ids = options.ids || '';

        // Clean up parameters
        url = url.replace(/&num=\d+/, '');
        url = url.replace(/&action=\w+/, '');
        url = url.replace(/&ids=[^&]*/, '');

        url += '&action=' + action;
        url += '&num=' + limit;
        if (ids) {
            url += '&ids=' + ids;
        }

        return url;
    }

    window.loadDynamicBuilderContent = function (container, options) {
        if (!container) return;
        
        const settings = options || {};
        const count = settings.count || container.getAttribute('data-count') || 3;
        const action = settings.action || container.getAttribute('data-action') || 'recent_posts';
        const ids = settings.ids || container.getAttribute('data-ids') || '';

        // Force load if options provided
        if (!options && container.getAttribute('data-loaded') === 'true') return;

        const apiUrl = getApiUrl({ count: count, action: action, ids: ids });

        fetch(apiUrl)
            .then(function (response) {
                return response.json();
            })
            .then(function (res) {
                if (res.status === 'success' && res.data && res.data.length > 0) {
                    renderCards(container, res.data, count);
                } else {
                    renderCards(container, null, count); // Use mock data
                }
            })
            .catch(function () {
                renderCards(container, null, count); // Use mock data
            });
    };

    window.loadDynamicBuilderContentFallback = function (container) {
        window.loadDynamicBuilderContent(container);
    };

    function initDynamicContentLoader() {
        // Initialization logic for content loading can go here
    }

    function loadDynamicBuilderContent(container, params = {}) {
        if (!container) return;
        const action = params.action || 'recent_posts';
        const count = params.count || 3;
        const ids = params.ids || '';
        
        // Build API URL robustly for GeniXCMS AJAX structure
        const config = window.dynamicBuilderConfig;
        let url = config.siteUrl;
        
        if (config.isSmartUrl) {
            url += 'ajax/api/public';
        } else {
            url += 'index.php?ajax=api&token=public';
        }

        url += '&action=' + action + '&num=' + count;
        if (ids) url += '&ids=' + ids;

        console.log('[Builder] Fetching dynamic content from:', url);

        fetch(url)
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(res => {
                console.log('[Builder] Dynamic API Response:', res);
                if (res.status === 'success' && Array.isArray(res.data) && res.data.length > 0) {
                    let html = '<div class="dynamic-posts-wrapper w-100">';
                    html += '<div class="row g-3 text-dark text-start">';

                    res.data.forEach(function (post) {
                        html += '<div class="col-md-4">';
                        html += '<div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white">';
                        html += '<div class="ratio ratio-16x9">';
                        html += '<img src="' + post.image + '" class="object-fit-cover w-100 h-100" alt="' + post.title + '">';
                        html += '</div>';
                        html += '<div class="card-body p-3">';
                        html += '<div class="d-flex justify-content-between align-items-center mb-2">';
                        html += '<span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-pill" style="font-size: 9px; font-weight: 700;">' + (post.category || 'NEWS').toUpperCase() + '</span>';
                        html += '<small class="text-muted" style="font-size: 9px;">' + (post.date || '') + '</small>';
                        html += '</div>';
                        html += '<h6 class="fw-bold mb-2 lh-base text-dark" style="font-size: 14px;">' + post.title + '</h6>';
                        html += '<p class="text-muted small mb-0 opacity-75" style="font-size: 12px; line-height: 1.5;">' + (post.excerpt ? post.excerpt.substring(0, 80) + '...' : '') + '</p>';
                        html += '</div></div></div>';
                    });

                    html += '</div></div>';
                    container.innerHTML = html;
                    container.setAttribute('data-loaded', 'true');
                } else {
                    container.innerHTML = '<div class="alert alert-info py-2 small text-center">No CMS data found.</div>';
                }
            })
            .catch(() => {
                container.innerHTML = '<div class="alert alert-danger">Failed to load content.</div>';
            });
    }

    window.loadDynamicBuilderContent = loadDynamicBuilderContent;

    function initDynamicContentLoader() {
        // This handles loading content for containers existing in the main editor (Summernote/GxEditor)
        const containers = document.querySelectorAll('.recent-posts-container');
        containers.forEach(c => loadDynamicBuilderContent(c));
    }

    function initBuilder() {
        let editor = null;

        function createEditor() {
            const config = window.dynamicBuilderConfig;
            
            // Asset Manager elFinder Integration
            const elFinderIntegration = function(editor) {
                const am = editor.AssetManager;
                
                editor.on('asset:open', () => {
                    const elFinderUrl = config.elfinderUrl;
                    if (!elFinderUrl) return;

                    const token = config.ajaxToken;
                    const url = elFinderUrl + (elFinderUrl.includes('?') ? '&' : '?') + 'token=' + token;

                    window.open(url, 'elFinder', 'width=900,height=600');

                    window.onMessage = function(event) {
                        if (event.data && event.data.url) {
                            am.add(event.data.url);
                            am.close();
                        }
                    };
                });
            };

            editor = grapesjs.init({
                container: '#gjs',
                fromElement: false,
                height: '100%',
                width: 'auto',
                storageManager: false,
                allowScripts: 1,
                avoidInlineStyle: 0,
                styleManager: {
                    sectors: [
                        {
                            name: 'General',
                            open: false,
                            buildProps: ['float', 'display', 'position', 'top', 'right', 'left', 'bottom'],
                        }, {
                            name: 'Layout',
                            open: true,
                            buildProps: ['width', 'height', 'max-width', 'min-height', 'margin', 'padding'],
                        }, {
                            name: 'Typography',
                            open: true,
                            buildProps: [],
                            properties: [
                                {
                                    property: 'font-family',
                                    name: 'Font Family',
                                    type: 'select',
                                    full: true,
                                    list: [
                                        { value: 'Inter, sans-serif', name: 'Inter' },
                                        { value: 'Plus Jakarta Sans, sans-serif', name: 'Jakarta Sans' },
                                        { value: 'Roboto, sans-serif', name: 'Roboto' },
                                        { value: 'Poppins, sans-serif', name: 'Poppins' },
                                        { value: 'Montserrat, sans-serif', name: 'Montserrat' },
                                        { value: 'Open Sans, sans-serif', name: 'Open Sans' },
                                        { value: 'Playfair Display, serif', name: 'Playfair Display' },
                                        { value: 'Lato, sans-serif', name: 'Lato' },
                                        { value: 'Georgia, serif', name: 'Georgia' },
                                        { value: 'Arial, Helvetica, sans-serif', name: 'Arial' }
                                    ]
                                },
                                {
                                    property: 'font-weight',
                                    name: 'Weight',
                                    type: 'select',
                                    full: false,
                                    list: [
                                        { value: '100', name: 'Thin' },
                                        { value: '300', name: 'Light' },
                                        { value: '400', name: 'Normal' },
                                        { value: '500', name: 'Medium' },
                                        { value: '600', name: 'Semi-Bold' },
                                        { value: '700', name: 'Bold' },
                                        { value: '800', name: 'Extra-Bold' },
                                        { value: '900', name: 'Black' }
                                    ]
                                },
                                {
                                    property: 'font-size',
                                    name: 'Size',
                                    full: false,
                                },
                                {
                                    property: 'color',
                                    name: 'Color',
                                    type: 'color',
                                    full: false,
                                },
                                {
                                    property: 'line-height',
                                    name: 'Line Height',
                                    full: false,
                                },
                                {
                                    property: 'letter-spacing',
                                    name: 'Spacing',
                                    full: false,
                                },
                                {
                                    property: 'text-align',
                                    name: 'Align',
                                    type: 'radio',
                                    full: true,
                                    list: [
                                        { value: 'left', name: 'Left', className: 'fa fa-align-left' },
                                        { value: 'center', name: 'Center', className: 'fa fa-align-center' },
                                        { value: 'right', name: 'Right', className: 'fa fa-align-right' },
                                        { value: 'justify', name: 'Justify', className: 'fa fa-align-justify' }
                                    ],
                                }
                            ]
                        }, {
                            name: 'Decorations',
                            open: false,
                            buildProps: [
                                'background-color', 
                                'background-image', 
                                'background-size', 
                                'background-position', 
                                'background-repeat',
                                'background-attachment',
                                'border-radius', 
                                'border', 
                                'box-shadow'
                            ],
                            properties: [
                                {
                                    property: 'background-image',
                                    name: 'Background Image',
                                    type: 'file',
                                    full: true,
                                },
                                {
                                    property: 'background-size',
                                    name: 'Size',
                                    type: 'select',
                                    list: [
                                        { value: 'auto', name: 'Auto' },
                                        { value: 'cover', name: 'Cover' },
                                        { value: 'contain', name: 'Contain' }
                                    ]
                                },
                                {
                                    property: 'background-attachment',
                                    name: 'Attachment',
                                    type: 'select',
                                    list: [
                                        { value: 'scroll', name: 'Scroll' },
                                        { value: 'fixed', name: 'Fixed (Parallax)' },
                                        { value: 'local', name: 'Local' }
                                    ]
                                }
                            ]
                        }, {
                            name: 'Extra',
                            open: false,
                            buildProps: ['opacity', 'transition', 'perspective', 'transform'],
                        }
                    ],
                },
                selectorManager: { componentFirst: true },
                plugins: [elFinderIntegration],
                deviceManager: {
                    devices: [
                       { name: 'Desktop', width: '' },
                       { name: 'Tablet', width: '768px', widthMedia: '992px' },
                       { name: 'Mobile', width: '320px', widthMedia: '480px' },
                    ]
                },
                allowScripts: 1,
                canvas: {
                    styles: [
                        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
                        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css',
                        'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
                        'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200',
                        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Roboto:wght@300;400;500;700&family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@300;400;600;700&family=Open+Sans:wght@300;400;600;700&family=Playfair+Display:wght@400;700&family=Lato:wght@300;400;700&display=swap',
                        window.dynamicBuilderConfig.siteUrl + 'inc/mod/dynamic-builder/assets/css/canvas.css'
                    ],
                    scripts: [
                        'https://code.jquery.com/jquery-3.7.1.min.js',
                        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js'
                    ]
                }
            });

            addBlocks(editor);
            registerDynamicComponents(editor);

            // Re-initialize Bootstrap interactive components (carousels, accordions) in the canvas
            // GrapesJS injects HTML AFTER Bootstrap JS loads, so data-bs-ride never auto-fires.
            function reinitCanvasBootstrap(canvasWindow) {
                if (!canvasWindow || !canvasWindow.bootstrap) return;
                try {
                    canvasWindow.document.querySelectorAll('[data-bs-ride="carousel"]').forEach(function (el) {
                        canvasWindow.bootstrap.Carousel.getOrCreateInstance(el, { ride: 'carousel', interval: 3500 });
                    });
                } catch (e) { /* ignore reinit errors */ }
            }

            editor.on('canvas:frame:load', function ({ window: cw }) {
                setTimeout(function () { reinitCanvasBootstrap(cw); }, 600);
            });

            editor.on('component:add', function () {
                const frames = editor.Canvas.getFrames();
                frames.forEach(function (frame) {
                    const cw = frame.view && frame.view.getWindow ? frame.view.getWindow() : null;
                    if (cw) setTimeout(function () { reinitCanvasBootstrap(cw); }, 400);
                });
            });

            // Breadcrumb Logic for Context Navigation
            const updateBreadcrumbs = (editor) => {
                const container = document.getElementById('breadcrumb-list');
                if (!container) return;
                
                const selected = editor.getSelected();
                if (!selected) {
                    container.innerHTML = '<span class="badge bg-secondary opacity-25" style="font-size:10px;">Select an element...</span>';
                    return;
                }

                let crumbs = [];
                let current = selected;
                while (current && current.get('tagName') !== 'wrapper') {
                    const name = current.getName() || current.get('tagName') || 'Element';
                    const type = current.get('type') || current.get('tagName');
                    const icons = {
                        'section': 'bi bi-view-stacked',
                        'row': 'bi bi-distribute-vertical',
                        'column': 'bi bi-columns-gap',
                        'text': 'bi bi-type',
                        'image': 'bi bi-image',
                        'tabs': 'bi bi-segmented-nav',
                        'accordion': 'bi bi-question-circle',
                        'process-steps': 'bi bi-list-ol',
                        'content-card-system': 'bi bi-card-image',
                        'custom-code': 'bi bi-code-slash'
                    };
                    const icon = icons[type] || 'bi bi-box';
                    crumbs.unshift({ name, icon, model: current });
                    current = current.parent();
                }

                container.innerHTML = '';
                crumbs.forEach((crumb, index) => {
                    const item = document.createElement('span');
                    item.className = 'badge bg-primary bg-opacity-25 text-white shadow-sm px-2 py-1 rounded-pill cursor-pointer hover-bg-primary';
                    item.style.fontSize = '10px';
                    item.style.cursor = 'pointer';
                    item.style.transition = 'all 0.2s';
                    item.innerHTML = `<i class="${crumb.icon} me-1"></i> ${crumb.name}`;
                    item.onclick = () => editor.select(crumb.model);
                    container.appendChild(item);

                    if (index < crumbs.length - 1) {
                        const sep = document.createElement('i');
                        sep.className = 'bi bi-chevron-right text-white-50 mx-1';
                        sep.style.fontSize = '8px';
                        container.appendChild(sep);
                    }
                });

                if (container.lastChild) {
                    container.lastChild.classList.replace('bg-opacity-25', 'bg-opacity-75');
                }
            };

            editor.on('component:selected', () => updateBreadcrumbs(editor));
            editor.on('load', () => updateBreadcrumbs(editor));

            // Register Textarea trait type for long code inputs
            editor.TraitManager.addType('textarea', {
                noLabel: true, // Hide the default side label
                createInput({ trait }) {
                    const container = document.createElement('div');
                    container.style.width = '100%';
                    container.style.padding = '10px 5px';
                    
                    const label = document.createElement('div');
                    label.innerHTML = `<i class="bi bi-code"></i> ${trait.get('label') || trait.get('name')}`;
                    label.style.marginBottom = '8px';
                    label.style.fontSize = '0.65rem';
                    label.style.fontWeight = 'bold';
                    label.style.color = '#a0aec0';
                    label.style.textTransform = 'uppercase';
                    label.style.letterSpacing = '0.05em';
                    
                    const el = document.createElement('textarea');
                    el.className = 'gjs-field-textarea';
                    el.placeholder = trait.get('placeholder') || '';
                    el.style.width = '100%';
                    el.style.minHeight = '200px';
                    el.style.padding = '10px';
                    el.style.backgroundColor = '#1a1a1a';
                    el.style.color = '#e2e8f0';
                    el.style.fontFamily = "'Fira Code', 'Cascadia Code', monospace";
                    el.style.fontSize = '12px';
                    el.style.lineHeight = '1.5';
                    el.style.border = '1px solid #333';
                    el.style.borderRadius = '6px';
                    el.style.outline = 'none';
                    el.style.resize = 'vertical';
                    el.style.transition = 'border-color 0.2s';
                    
                    el.onfocus = () => el.style.borderColor = '#0d6efd';
                    el.onblur = () => el.style.borderColor = '#333';

                    container.appendChild(label);
                    container.appendChild(el);
                    return container;
                },
                onEvent(options, secondArg) {
                    const el = (options && options.el) ? options.el : options;
                    const trait = (options && options.trait) ? options.trait : secondArg;
                    if (el && el.querySelector && trait) {
                        const input = el.querySelector('textarea');
                        if (input) trait.setTargetValue(input.value);
                    }
                },
                onUpdate(options, secondArg) {
                    const el = (options && options.el) ? options.el : options;
                    const trait = (options && options.trait) ? options.trait : secondArg;
                    if (el && el.querySelector && trait) {
                        const input = el.querySelector('textarea');
                        if (input) {
                            input.value = trait.getTargetValue() || '';
                        }
                    }
                },
            });

            // Command to Add Column
            editor.Commands.add('add-column', {
                run(editor, sender) {
                    let selected = editor.getSelected();
                    if (!selected) return;

                    // If a column is selected, we target its parent row
                    if (selected.is('column')) {
                        selected = selected.parent();
                    }

                    if (selected && selected.is('row')) {
                        selected.append({
                            type: 'column',
                            style: { },
                            attributes: { class: 'col-md-4' } // Reasonable default size
                        });
                    }
                }
            });

            // Command to add a new FAQ item
            editor.Commands.add('add-faq-item', {
                run(editor, sender, opts = {}) {
                    const selected = editor.getSelected();
                    if (!selected) return;

                    const accordion = selected.closest('.accordion');
                    if (!accordion) return;

                    const items = accordion.get('components');
                    const newId = 'flush-' + Math.random().toString(36).substr(2, 9);
                    
                    items.add({
                        tagName: 'div',
                        attributes: { class: 'accordion-item border-bottom' },
                        components: [
                            { 
                                tagName: 'h2', 
                                attributes: { class: 'accordion-header' }, 
                                components: [{ 
                                    tagName: 'button', 
                                    attributes: { 
                                        class: 'accordion-button collapsed fw-bold py-3', 
                                        type: 'button', 
                                        'data-bs-toggle': 'collapse', 
                                        'data-bs-target': `#${newId}` 
                                    }, 
                                    content: 'New Question Goes Here?' 
                                }] 
                            },
                            { 
                                tagName: 'div', 
                                attributes: { id: newId, class: 'accordion-collapse collapse' }, 
                                components: [{ 
                                    tagName: 'div', 
                                    attributes: { class: 'accordion-body text-secondary' }, 
                                    content: 'Insert the answer for your new FAQ item here.' 
                                }] 
                            }
                        ]
                    });
                }
            });

            // Add Plus Button to Row/Column Toolbar
            editor.on('component:selected', () => {
                const selected = editor.getSelected();
                if (selected && (selected.is('row') || selected.is('column'))) {
                    const toolbar = selected.get('toolbar');
                    const hasAdd = toolbar.some(btn => btn.id === 'add-column-btn');
                    
                    if (!hasAdd) {
                        toolbar.unshift({
                            id: 'add-column-btn',
                            attributes: { class: 'fa fa-plus-circle', title: 'Add New Column' },
                            command: 'add-column',
                        });
                        selected.set('toolbar', toolbar);
                    }
                }
            });

            editor.DomComponents.addType('accordion', {
                isComponent: el => (el.getAttribute && el.getAttribute('class') && el.getAttribute('class').includes('accordion')),
                model: {
                    defaults: {
                        name: 'Accordion Container',
                        draggable: true,
                        droppable: '.accordion-item',
                    },
                    init() {
                        const toolbar = this.get('toolbar');
                        const hasButton = toolbar.some(btn => btn.command === 'add-faq-item');
                        if (!hasButton) {
                            toolbar.unshift({
                                attributes: { class: 'fa fa-plus', title: 'Add FAQ Item' },
                                command: 'add-faq-item',
                            });
                            this.set('toolbar', toolbar);
                        }
                    }
                }
            });

            // Command to add a new Process Step
            editor.Commands.add('add-process-step', {
                run(editor, sender) {
                    const selected = editor.getSelected();
                    if (!selected) return;

                    const container = selected.closest('[data-gjs-type="process-steps"]');
                    if (!container) return;

                    const stepCount = container.get('components').length + 1;
                    
                    container.append({
                        type: 'column',
                        attributes: { class: 'col-md-4' },
                        components: [{
                            tagName: 'div',
                            attributes: { class: 'h-100 p-5 bg-white rounded-5 shadow-sm border border-light text-center position-relative overflow-hidden' },
                            components: [
                                { tagName: 'div', attributes: { class: 'display-1 fw-bold opacity-10 position-absolute top-0 end-0 me-n3 mt-n3' }, content: stepCount.toString() },
                                { tagName: 'div', attributes: { class: 'btn btn-primary rounded-circle mb-4 p-0 d-flex align-items-center justify-content-center mx-auto', style: 'width:60px; height:60px;' }, components: [{ tagName: 'i', attributes: { class: 'bi bi-lightning-charge-fill fs-3' } }] },
                                { tagName: 'h3', attributes: { class: 'fs-4 fw-bold mb-3' }, content: `New Step ${stepCount}` },
                                { tagName: 'p', attributes: { class: 'text-secondary mb-0' }, content: 'Short description for the new process step.' }
                            ]
                        }]
                    });
                }
            });

            editor.Commands.add('add-carousel-slide', {
                run(editor, sender) {
                    const selected = editor.getSelected();
                    if (!selected) return;

                    const carousel = selected.closest('[data-gjs-type="bootstrap-carousel"]');
                    if (!carousel) return;

                    const inner = carousel.find('.carousel-inner')[0];
                    const indicators = carousel.find('.carousel-indicators')[0];
                    
                    if (inner) {
                        const slideIndex = inner.components().length;
                        const carouselId = carousel.getAttributes().id || 'carousel-' + Math.random().toString(36).substr(2, 5);
                        if (!carousel.getAttributes().id) carousel.addAttributes({ id: carouselId });

                        // 1. Add Slide Content
                        inner.append({
                            tagName: 'div',
                            attributes: { class: 'carousel-item' },
                            components: [
                                {
                                    type: 'image',
                                    attributes: { 
                                        src: 'https://images.unsplash.com/photo-1497215728101-856f4ea42174?auto=format&fit=crop&q=80&w=1200', 
                                        class: 'd-block w-100',
                                        style: 'height: 450px; object-fit: cover;'
                                    }
                                },
                                {
                                    tagName: 'div',
                                    attributes: { class: 'carousel-caption d-none d-md-block bg-black bg-opacity-50 rounded-4 p-4 mb-4' },
                                    components: [
                                        { type: 'text', tagName: 'h3', attributes: { class: 'fw-bold text-white' }, content: 'New Slide Title' },
                                        { type: 'text', tagName: 'p', content: 'Description for the newly added slide.' }
                                    ]
                                }
                            ]
                        });

                        // 2. Add Indicator
                        if (indicators) {
                            indicators.append({
                                tagName: 'button',
                                attributes: { 
                                    type: 'button', 
                                    'data-bs-target': `#${carouselId}`, 
                                    'data-bs-slide-to': slideIndex.toString() 
                                }
                            });
                        }

                        if (window.toastr) window.toastr.success('New slide added. Edit images and text directly.');
                    }
                }
            });

            editor.Commands.add('remove-carousel-slide', {
                run(editor, sender) {
                    const selected = editor.getSelected();
                    if (!selected) return;

                    const carousel = selected.closest('[data-gjs-type="bootstrap-carousel"]');
                    if (!carousel) return;

                    const inner = carousel.find('.carousel-inner')[0];
                    const indicators = carousel.find('.carousel-indicators')[0];
                    if (!inner) return;

                    const slides = inner.components();
                    if (slides.length <= 1) {
                        if (window.toastr) window.toastr.warning('At least one slide is required.');
                        return;
                    }

                    // Remove the last slide
                    slides.remove(slides.at(slides.length - 1));

                    // Remove the last indicator
                    if (indicators) {
                        const dots = indicators.components();
                        if (dots.length > 0) {
                            dots.remove(dots.at(dots.length - 1));
                        }
                    }

                    if (window.toastr) window.toastr.info('Slide removed.');
                    editor.select(carousel);
                }
            });

            // add-carousel-item — smart: gx-carousel-track (gallery) > carousel-inner (slider) > row (grid)
            editor.Commands.add('add-carousel-item', {
                run(editor, sender) {
                    const selected = editor.getSelected();
                    if (!selected) return;

                    // 1. Multi-item gallery carousel (gx-carousel-track)
                    const track = selected.find('.gx-carousel-track')[0];
                    if (track) {
                        track.append({
                            tagName: 'div',
                            attributes: { class: 'gx-carousel-slide flex-shrink-0' },
                            style: { width: 'calc(33.333% - 16px)' },
                            components: [{
                                tagName: 'div',
                                attributes: { class: 'card border-0 shadow-sm rounded-4 overflow-hidden h-100' },
                                components: [
                                    { type: 'image', attributes: { src: 'https://images.unsplash.com/photo-1497215728101-856f4ea42174?auto=format&fit=crop&q=80&w=600', class: 'card-img-top', style: 'height: 220px; object-fit: cover;' } },
                                    { tagName: 'div', attributes: { class: 'card-body p-3' }, components: [
                                        { type: 'text', tagName: 'h6', attributes: { class: 'fw-bold mb-1' }, content: 'New Image' },
                                        { type: 'text', tagName: 'p', attributes: { class: 'small text-secondary mb-0' }, content: 'Short description here' }
                                    ]}
                                ]
                            }]
                        });
                        return;
                    }

                    // 2. Full-width Bootstrap carousel (carousel-inner)
                    const inner = selected.find('.carousel-inner')[0];
                    if (inner) {
                        let carouselId = selected.getAttributes().id;
                        if (!carouselId) {
                            carouselId = 'img-carousel-' + Math.random().toString(36).substr(2, 6);
                            selected.addAttributes({ id: carouselId });
                            selected.find('.carousel-indicators button').forEach(btn => btn.addAttributes({ 'data-bs-target': '#' + carouselId }));
                            selected.find('[data-bs-slide]').forEach(btn => btn.addAttributes({ 'data-bs-target': '#' + carouselId }));
                        }
                        const currentCount = inner.components().length;
                        inner.append({
                            tagName: 'div',
                            attributes: { class: 'carousel-item' },
                            components: [
                                { type: 'image', attributes: { src: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=1200', class: 'd-block w-100', style: 'height: 420px; object-fit: cover;' } },
                                { tagName: 'div', attributes: { class: 'carousel-caption d-none d-md-block bg-black bg-opacity-50 rounded-4 p-4 mb-4' }, components: [
                                    { type: 'text', tagName: 'h5', attributes: { class: 'fw-bold text-white' }, content: 'New Slide' },
                                    { type: 'text', tagName: 'p', attributes: { class: 'text-white-50 small mb-0' }, content: 'Click to edit this caption' }
                                ]}
                            ]
                        });
                        const indicators = selected.find('.carousel-indicators')[0];
                        if (indicators) {
                            indicators.append({ tagName: 'button', attributes: { type: 'button', 'data-bs-slide-to': String(currentCount), 'data-bs-target': '#' + carouselId } });
                        }
                        return;
                    }

                    // 3. Grid-based fallback (testimonial slider)
                    const row = selected.find('.row')[0] || selected;
                    row.append({
                        tagName: 'div',
                        attributes: { class: 'col-md-6' },
                        components: [{
                            tagName: 'div',
                            attributes: { class: 'p-4 bg-white rounded-4 shadow-sm border border-light h-100' },
                            components: [
                                { tagName: 'div', attributes: { class: 'd-flex align-items-center mb-3' }, components: [
                                    { type: 'image', attributes: { src: 'https://i.pravatar.cc/100?u=' + Date.now(), class: 'rounded-circle me-3', width: '50' } },
                                    { tagName: 'div', components: [{ type: 'text', tagName: 'h6', attributes: { class: 'fw-bold mb-0' }, content: 'New Name' }, { type: 'text', tagName: 'p', attributes: { class: 'small text-muted mb-0' }, content: 'Position @ Company' }] }
                                ]},
                                { type: 'text', attributes: { class: 'small text-secondary mb-0 italic' }, content: '"New testimonial content goes here."' }
                            ]
                        }]
                    });
                }
            });

            editor.Commands.add('remove-carousel-item', {
                run(editor, sender) {
                    const selected = editor.getSelected();
                    if (!selected) return;

                    // 1. Multi-item gallery
                    const track = selected.find('.gx-carousel-track')[0];
                    if (track) {
                        const slides = track.components();
                        if (slides.length > 1) slides.remove(slides.at(slides.length - 1));
                        return;
                    }

                    // 2. Bootstrap full-width carousel
                    const inner = selected.find('.carousel-inner')[0];
                    if (inner) {
                        const items = inner.components();
                        if (items.length > 1) {
                            items.remove(items.at(items.length - 1));
                            const indicators = selected.find('.carousel-indicators')[0];
                            if (indicators && indicators.components().length > 1) {
                                const inds = indicators.components();
                                inds.remove(inds.at(inds.length - 1));
                            }
                        }
                        return;
                    }

                    // 3. Grid fallback
                    const row = selected.find('.row')[0] || selected;
                    const items = row.components();
                    if (items.length > 1) items.remove(items.at(items.length - 1));
                }
            });

            // Testimony Carousel Commands
            editor.Commands.add('add-testi-slide', {
                run(editor, sender) {
                    const selected = editor.getSelected();
                    if (!selected) return;

                    // Multi-item gallery testi carousel
                    const track = selected.find('.gx-testi-track')[0];
                    if (track) {
                        const cols = parseInt(selected.getAttributes()['data-columns'] || 2);
                        const width = (100 / cols);
                        const gap = (24 * (cols - 1) / cols);
                        const avatarId = Math.floor(Math.random() * 70) + 1;
                        track.append({
                            tagName: 'div',
                            attributes: { class: 'gx-testi-slide flex-shrink-0' },
                            style: { width: `calc(${width.toFixed(3)}% - ${gap.toFixed(3)}px)` },
                            components: [{
                                tagName: 'div',
                                attributes: { class: 'p-5 bg-white rounded-5 shadow-sm border border-light h-100 text-center' },
                                components: [
                                    { type: 'image', attributes: { src: 'https://i.pravatar.cc/100?u=' + avatarId, class: 'rounded-circle mb-4 border border-4 border-primary border-opacity-25 shadow-sm', width: '80', height: '80' } },
                                    { tagName: 'div', attributes: { class: 'mb-3 text-warning small' }, content: '<i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>' },
                                    { type: 'text', tagName: 'p', attributes: { class: 'fs-6 text-dark fw-medium italic mb-4 lh-lg' }, content: '"New testimonial content goes here. Share your customer\'s experience."' },
                                    { type: 'text', tagName: 'h6', attributes: { class: 'fw-bold mb-1' }, content: 'New Person' },
                                    { type: 'text', tagName: 'p', attributes: { class: 'small text-secondary mb-0' }, content: 'Position / Company' }
                                ]
                            }]
                        });
                        return;
                    }

                    // Fallback: old Bootstrap single-item carousel-inner
                    const inner = selected.find('.carousel-inner')[0];
                    if (inner) {
                        inner.append({
                            tagName: 'div',
                            attributes: { class: 'carousel-item px-5' },
                            components: [
                                { type: 'image', attributes: { src: 'https://i.pravatar.cc/100?u=' + Math.random(), class: 'rounded-circle mb-4 border border-4 border-white shadow-sm', width: '80' } },
                                { type: 'text', attributes: { class: 'fs-5 text-dark fw-medium px-md-5 italic mb-4' }, content: '"New testimonial content goes here..."' },
                                { type: 'text', tagName: 'h6', attributes: { class: 'fw-bold mb-0' }, content: 'Person Name' },
                                { type: 'text', tagName: 'p', attributes: { class: 'small text-secondary' }, content: 'Position / Company' }
                            ]
                        });
                    }
                }
            });

            // Counter Widget Commands
            editor.Commands.add('add-counter-item', {
                run(editor, sender) {
                    const selected = editor.getSelected();
                    if (!selected) return;

                    const row = (selected.getAttributes()['data-gjs-type'] === 'bootstrap-counter-row') ? selected : selected.closest('[data-gjs-type="bootstrap-counter-row"]');
                    if (row) {
                        row.append({
                            type: 'bootstrap-counter-item',
                            attributes: { class: 'col-md-3' },
                            components: [{
                                tagName: 'div',
                                attributes: { class: 'p-4 bg-white rounded-4 shadow-sm border border-light h-100' },
                                components: [
                                    { tagName: 'i', attributes: { class: 'bi bi-plus-circle-fill text-muted display-5 mb-3 d-block' } },
                                    { type: 'text', tagName: 'h2', attributes: { class: 'fw-bold mb-1', 'data-target': '100' }, content: '100' },
                                    { type: 'text', tagName: 'p', attributes: { class: 'small text-uppercase text-secondary fw-bold tracking-widest mb-0' }, content: 'Label' }
                                ]
                            }]
                        });
                    }
                }
            });

            editor.Commands.add('remove-counter-item', {
                run(editor, sender) {
                    const selected = editor.getSelected();
                    if (selected) {
                        const row = (selected.getAttributes()['data-gjs-type'] === 'bootstrap-counter-row') ? selected : selected.closest('[data-gjs-type="bootstrap-counter-row"]');
                        if (row) {
                            const children = row.components();
                            if (children.length > 1) children.remove(children.at(children.length - 1));
                        }
                    }
                }
            });

            editor.Commands.add('add-team-item', {
                run(editor, sender) {
                    const selected = editor.getSelected();
                    if (!selected) return;

                    const row = selected.find('[data-gjs-type="bootstrap-team-row"]')[0] || selected.closest('[data-gjs-type="bootstrap-team-row"]');
                    if (row) {
                        row.append({
                            type: 'bootstrap-team-item',
                            attributes: { class: 'col-md-3' },
                            components: [{
                                tagName: 'div',
                                attributes: { class: 'gx-team-card text-center' },
                                components: [
                                    { 
                                      tagName: 'div', 
                                      attributes: { class: 'ratio ratio-3x4 mb-4 overflow-hidden border border-light bg-secondary bg-opacity-10' }, 
                                      components: [{ 
                                        type: 'image', 
                                        attributes: { 
                                            src: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&q=80', 
                                            class: 'w-100 h-100 object-cover grayscale transition-700 position-absolute top-0 start-0' 
                                        } 
                                      }] 
                                    },
                                    { type: 'text', tagName: 'h4', attributes: { class: 'h5 fw-bold mb-1' }, content: 'New Member' },
                                    { type: 'text', tagName: 'p', attributes: { class: 'small text-primary fw-bold text-uppercase tracking-widest mb-0' }, content: 'Position Name' }
                                ]
                            }]
                        });
                    }
                }
            });

            editor.Commands.add('remove-team-item', {
                run(editor, sender) {
                    const selected = editor.getSelected();
                    if (selected) {
                        const row = selected.find('[data-gjs-type="bootstrap-team-row"]')[0] || selected.closest('[data-gjs-type="bootstrap-team-row"]');
                        if (row) {
                            const children = row.components();
                            if (children.length > 1) children.remove(children.at(children.length - 1));
                        }
                    }
                }
            });

            editor.Commands.add('remove-testi-slide', {
                run(editor, sender) {
                    const selected = editor.getSelected();
                    if (!selected) return;

                    // Multi-item gallery
                    const track = selected.find('.gx-testi-track')[0];
                    if (track) {
                        const slides = track.components();
                        if (slides.length > 1) slides.remove(slides.at(slides.length - 1));
                        return;
                    }

                    // Fallback: Bootstrap single carousel
                    const inner = selected.find('.carousel-inner')[0];
                    if (inner && inner.components().length > 1) {
                        inner.components().remove(inner.components().at(inner.components().length - 1));
                    }
                }
            });

            editor.DomComponents.addType('bootstrap-carousel', {
                model: {
                    defaults: {
                        name: 'Image Slider',
                        draggable: true,
                        droppable: false,
                        // Bootstrap carousel init script — runs inside the canvas iframe
                        script: function () {
                            if (typeof bootstrap !== 'undefined') {
                                bootstrap.Carousel.getOrCreateInstance(this, { ride: 'carousel', interval: 4000 });
                            }
                        }
                    },
                    init() {
                        // Auto-assign ID so indicators & controls can target correctly
                        if (!this.getAttributes().id) {
                            const id = 'slider-' + Math.random().toString(36).substr(2, 6);
                            this.addAttributes({ id });
                            this.find('.carousel-indicators button').forEach(btn => {
                                btn.addAttributes({ 'data-bs-target': '#' + id });
                            });
                            this.find('[data-bs-slide]').forEach(btn => {
                                btn.addAttributes({ 'data-bs-target': '#' + id });
                            });
                        }
                        const toolbar = this.get('toolbar');
                        const hasButton = toolbar.some(btn => btn.command === 'add-carousel-slide');
                        if (!hasButton) {
                            toolbar.unshift({ attributes: { class: 'fa fa-plus-square', title: 'Add Slide' }, command: 'add-carousel-slide' });
                            toolbar.unshift({ attributes: { class: 'fa fa-minus-square', title: 'Remove Slide' }, command: 'remove-carousel-slide' });
                            this.set('toolbar', toolbar);
                        }
                    }
                }
            });

            editor.DomComponents.addType('bootstrap-image-carousel', {
                model: {
                    defaults: {
                        name: 'Image Carousel',
                        tagName: 'div',
                        draggable: true,
                        droppable: false,
                        // Multi-item gallery carousel script — runs inside the canvas iframe
                        script: function () {
                            var el = this;
                            var track = el.querySelector('.gx-carousel-track');
                            if (!track) return;
                            var slides = el.querySelectorAll('.gx-carousel-slide');
                            if (!slides.length) return;
                            var prevBtn = el.querySelector('.gx-carousel-prev');
                            var nextBtn = el.querySelector('.gx-carousel-next');
                            var currentIndex = 0;
                            var visibleCount = 3;

                            function getSlideWidth() {
                                return slides[0].offsetWidth + 24; // 24px = gap
                            }

                            function clamp(val, min, max) {
                                return Math.max(min, Math.min(max, val));
                            }

                            function updateTrack() {
                                var maxIndex = Math.max(0, slides.length - visibleCount);
                                currentIndex = clamp(currentIndex, 0, maxIndex);
                                track.style.transform = 'translateX(-' + (currentIndex * getSlideWidth()) + 'px)';
                                if (prevBtn) prevBtn.style.opacity = currentIndex === 0 ? '0.4' : '1';
                                if (nextBtn) nextBtn.style.opacity = currentIndex >= maxIndex ? '0.4' : '1';
                            }

                            if (prevBtn) {
                                prevBtn.addEventListener('click', function (e) {
                                    e.preventDefault(); e.stopPropagation();
                                    currentIndex--; updateTrack();
                                });
                            }
                            if (nextBtn) {
                                nextBtn.addEventListener('click', function (e) {
                                    e.preventDefault(); e.stopPropagation();
                                    currentIndex++; updateTrack();
                                });
                            }
                            updateTrack();
                        }
                    },
                    init() {
                        const toolbar = this.get('toolbar');
                        const hasBtn = toolbar.some(b => b.command === 'add-carousel-item');
                        if (!hasBtn) {
                            toolbar.unshift({ attributes: { class: 'fa fa-plus-square', title: 'Add Image Card' }, command: 'add-carousel-item' });
                            toolbar.unshift({ attributes: { class: 'fa fa-minus-square', title: 'Remove Image Card' }, command: 'remove-carousel-item' });
                            this.set('toolbar', toolbar);
                        }
                    }
                }
            });

            editor.DomComponents.addType('bootstrap-testi-carousel', {
                model: {
                    defaults: {
                        name: 'Testi Carousel',
                        tagName: 'div',
                        draggable: true,
                        droppable: false,
                        traits: [
                            {
                                type: 'select',
                                label: 'Columns',
                                name: 'data-columns',
                                options: [
                                    { value: '1', name: '1 Column' },
                                    { value: '2', name: '2 Columns' },
                                    { value: '3', name: '3 Columns' },
                                    { value: '4', name: '4 Columns' }
                                ]
                            }
                        ],
                        // Multi-item testimonial gallery script — runs inside canvas iframe
                        script: function () {
                            var el = this;
                            var track = el.querySelector('.gx-testi-track');
                            if (!track) return;
                            var slides = el.querySelectorAll('.gx-testi-slide');
                            if (!slides.length) return;
                            var prevBtn = el.querySelector('.gx-testi-prev');
                            var nextBtn = el.querySelector('.gx-testi-next');
                            var currentIndex = 0;
                            var visibleCount = parseInt(el.getAttribute('data-columns') || 2);

                            function getSlideWidth() {
                                return slides[0].offsetWidth + 24;
                            }

                            function clamp(val, min, max) {
                                return Math.max(min, Math.min(max, val));
                            }

                            function updateTrack() {
                                var maxIndex = Math.max(0, slides.length - visibleCount);
                                currentIndex = clamp(currentIndex, 0, maxIndex);
                                track.style.transform = 'translateX(-' + (currentIndex * getSlideWidth()) + 'px)';
                                if (prevBtn) prevBtn.style.opacity = currentIndex === 0 ? '0.4' : '1';
                                if (nextBtn) nextBtn.style.opacity = currentIndex >= maxIndex ? '0.4' : '1';
                            }

                            if (prevBtn) {
                                prevBtn.addEventListener('click', function (e) {
                                    e.preventDefault(); e.stopPropagation();
                                    currentIndex--; updateTrack();
                                });
                            }
                            if (nextBtn) {
                                nextBtn.addEventListener('click', function (e) {
                                    e.preventDefault(); e.stopPropagation();
                                    currentIndex++; updateTrack();
                                });
                            }
                            updateTrack();
                        }
                    },
                    init() {
                        const toolbar = this.get('toolbar');
                        const hasBtn = toolbar.some(b => b.command === 'add-testi-slide');
                        if (!hasBtn) {
                            toolbar.unshift({ attributes: { class: 'fa fa-plus-square', title: 'Add Testimonial' }, command: 'add-testi-slide' });
                            toolbar.unshift({ attributes: { class: 'fa fa-minus-square', title: 'Remove Testimonial' }, command: 'remove-testi-slide' });
                            this.set('toolbar', toolbar);
                        }
                        this.listenTo(this, 'change:attributes:data-columns', this.handleColumnChange);
                    },
                    handleColumnChange() {
                        const cols = parseInt(this.getAttributes()['data-columns'] || 2);
                        const slides = this.find('.gx-testi-slide');
                        const width = (100 / cols);
                        const gap = (24 * (cols - 1) / cols);
                        slides.forEach(slide => {
                            slide.addStyle({ width: `calc(${width.toFixed(3)}% - ${gap.toFixed(3)}px)` });
                        });
                        if (this.view) this.view.render();
                    }
                }
            });

            editor.DomComponents.addType('bootstrap-testi-slider', {
                model: {
                    defaults: {
                        name: 'Testi Slider',
                        draggable: true,
                        droppable: false,
                        script: function () {
                            if (typeof bootstrap !== 'undefined') {
                                bootstrap.Carousel.getOrCreateInstance(this, { ride: 'carousel', interval: 5000 });
                            }
                        }
                    },
                    init() {
                        // Auto-assign ID so controls can target correctly
                        if (!this.getAttributes().id) {
                            const id = 'testi-slider-' + Math.random().toString(36).substr(2, 6);
                            this.addAttributes({ id });
                            this.find('[data-bs-slide]').forEach(btn => {
                                btn.addAttributes({ 'data-bs-target': '#' + id });
                            });
                        }
                        const toolbar = this.get('toolbar');
                        const hasBtn = toolbar.some(b => b.command === 'add-testi-slide');
                        if (!hasBtn) {
                            toolbar.unshift({ attributes: { class: 'fa fa-plus-square', title: 'Add Testimonial' }, command: 'add-testi-slide' });
                            toolbar.unshift({ attributes: { class: 'fa fa-minus-square', title: 'Remove Testimonial' }, command: 'remove-testi-slide' });
                            this.set('toolbar', toolbar);
                        }
                    }
                }
            });

            editor.DomComponents.addType('bootstrap-counter-row', {
                model: {
                    defaults: { 
                        name: 'Counter Row', 
                        draggable: true, 
                        droppable: 'bootstrap-counter-item' 
                    },
                    init() {
                        const toolbar = this.get('toolbar');
                        const hasBtn = toolbar.some(b => b.command === 'add-counter-item');
                        if (!hasBtn) {
                            toolbar.unshift({ attributes: { class: 'fa fa-plus-circle', title: 'Add Counter Box' }, command: 'add-counter-item' });
                            toolbar.unshift({ attributes: { class: 'fa fa-minus-circle', title: 'Remove Last Box' }, command: 'remove-counter-item' });
                            this.set('toolbar', toolbar);
                        }
                    }
                }
            });

            editor.DomComponents.addType('bootstrap-counter-item', {
                model: {
                    defaults: { 
                        name: 'Counter Box',
                        draggable: 'bootstrap-counter-row',
                        droppable: false,
                        script: function () {
                            var el = this;
                            var h2 = el.querySelector('h2');
                            if (!h2) return;
                            
                            // Try to detect the target number from attributes or text
                            var valStr = h2.getAttribute('data-target') || h2.innerText;
                            var match = valStr.match(/(\d+)/);
                            if (!match) return;
                            
                            var target = parseInt(match[0]);
                            var suffix = valStr.replace(match[0], '').trim();
                            
                            var current = 0;
                            var duration = 2000;
                            var startTime = null;

                            function animate(timestamp) {
                                if (!startTime) startTime = timestamp;
                                var progress = Math.min((timestamp - startTime) / duration, 1);
                                var easeProgress = progress * (2 - progress); // Ease out
                                var value = Math.floor(easeProgress * target);
                                h2.innerText = value + suffix;
                                if (progress < 1) requestAnimationFrame(animate);
                            }

                            var observer = new IntersectionObserver(function(entries) {
                                if (entries[0].isIntersecting) {
                                    requestAnimationFrame(animate);
                                    observer.disconnect();
                                }
                            }, { threshold: 0.1 });
                            observer.observe(el);
                        }
                    }
                }
            });

            editor.DomComponents.addType('bootstrap-team-section', {
                model: {
                    defaults: { name: 'Team Section', draggable: true, droppable: true },
                    init() {
                        const toolbar = this.get('toolbar');
                        const hasBtn = toolbar.some(b => b.command === 'add-team-item');
                        if (!hasBtn) {
                            toolbar.unshift({ attributes: { class: 'fa fa-user-plus', title: 'Add Member' }, command: 'add-team-item' });
                            toolbar.unshift({ attributes: { class: 'fa fa-user-times', title: 'Remove Member' }, command: 'remove-team-item' });
                            this.set('toolbar', toolbar);
                        }
                    }
                }
            });

            editor.DomComponents.addType('bootstrap-team-row', {
                model: {
                    defaults: { name: 'Team Row', draggable: false, droppable: 'bootstrap-team-item' }
                }
            });

            editor.DomComponents.addType('bootstrap-team-item', {
                model: {
                    defaults: {
                        name: 'Team Member',
                        draggable: 'bootstrap-team-row',
                        droppable: true,
                        script: function() {
                            var styleId = 'gx-team-styles';
                            if (!document.getElementById(styleId)) {
                                var style = document.createElement('style');
                                style.id = styleId;
                                style.innerHTML = `
                                    .gx-team-card { cursor: pointer; transition: all 0.3s ease; }
                                    .gx-team-card .transition-700 { transition: all 0.7s ease-in-out !important; }
                                    .gx-team-card .grayscale { filter: grayscale(1); }
                                    .gx-team-card:hover .grayscale { filter: grayscale(0) !important; transform: scale(1.1); }
                                    .gx-team-card:hover { transform: translateY(-5px); }
                                `;
                                document.head.appendChild(style);
                            }
                        }
                    }
                }
            });

            editor.DomComponents.addType('process-steps', {
                isComponent: el => (el.getAttribute && el.getAttribute('class') && el.getAttribute('class').includes('process-container')),
                model: {
                    defaults: {
                        name: 'Process Steps',
                        draggable: true,
                        droppable: '.col, [class*="col-"]',
                    },
                    init() {
                        const toolbar = this.get('toolbar');
                        const hasButton = toolbar.some(btn => btn.command === 'add-process-step');
                        if (!hasButton) {
                            toolbar.unshift({
                                attributes: { class: 'fa fa-plus-circle', title: 'Add New Step' },
                                command: 'add-process-step',
                            });
                            this.set('toolbar', toolbar);
                        }
                    }
                }
            });
            editor.Commands.add('add-tab-item', {
                run(editor, sender) {
                    const selected = editor.getSelected();
                    if (!selected) return;

                    const tabsContainer = selected.closest('[data-gjs-type="tabs"]');
                    if (!tabsContainer) return;

                    const nav = tabsContainer.find('.nav-tabs')[0];
                    const content = tabsContainer.find('.tab-content')[0];
                    if (!nav || !content) return;

                    const newId = 'tab-' + Math.random().toString(36).substr(2, 9);
                    const tabCount = nav.get('components').length + 1;

                    // Add Nav Link
                    nav.append({
                        tagName: 'li',
                        attributes: { class: 'nav-item' },
                        components: [{
                            type: 'text',
                            tagName: 'button',
                            editable: true,
                            attributes: { 
                                class: 'nav-link fw-bold px-4 py-3 border-0 bg-transparent', 
                                id: `${newId}-tab`,
                                'data-bs-toggle': 'tab',
                                'data-bs-target': `#${newId}`,
                                type: 'button'
                            },
                            content: `Tab ${tabCount}`
                        }]
                    });

                    // Add Content Pane
                    content.append({
                        tagName: 'div',
                        attributes: { class: 'tab-pane fade p-4', id: newId, role: 'tabpanel' },
                        components: [{ content: `<h3>Content for Tab ${tabCount}</h3><p class="text-secondary">Edit your content here...</p>` }]
                    });
                }
            });

            editor.DomComponents.addType('tabs', {
                isComponent: el => (el.getAttribute && el.getAttribute('class') && el.getAttribute('class').includes('tabs-container')),
                model: {
                    defaults: {
                        name: 'Tabs System',
                        draggable: true,
                        droppable: true,
                        layout: 'horizontal',
                        traits: [
                            {
                                type: 'select',
                                label: 'Layout',
                                name: 'layout',
                                changeProp: 1, // CRITICAL: This allows the trait to trigger change:layout
                                options: [
                                    { id: 'horizontal', name: 'Horizontal' },
                                    { id: 'vertical', name: 'Vertical' }
                                ]
                            }
                        ]
                    },
                    init() {
                        const toolbar = this.get('toolbar');
                        const hasButton = toolbar.some(btn => btn.command === 'add-tab-item');
                        if (!hasButton) {
                            toolbar.unshift({
                                attributes: { class: 'fa fa-plus-square', title: 'Add New Tab' },
                                command: 'add-tab-item',
                            });
                            this.set('toolbar', toolbar);
                        }
                        this.listenTo(this, 'change:layout', this.handleLayoutChange);
                    },
                    handleLayoutChange() {
                        const layout = this.get('layout');
                        let nav = this.find('ul')[0];
                        if (!nav) nav = this.get('components').at(0);

                        console.log('Layout Switch Triggered:', layout);

                        if (layout === 'vertical') {
                            this.addClass('d-flex');
                            this.addClass('align-items-start');
                            if (nav) {
                                nav.removeStyle('min-width');
                                nav.addStyle({ 'min-width': '200px' });
                                nav.addClass('flex-column');
                                nav.addClass('nav-pills');
                                nav.addClass('me-3');
                                nav.removeClass('nav-tabs');
                            }
                        } else {
                            this.removeClass('d-flex');
                            this.removeClass('align-items-start');
                            if (nav) {
                                nav.removeStyle('min-width');
                                nav.removeClass('flex-column');
                                nav.removeClass('nav-pills');
                                nav.removeClass('me-3');
                                nav.addClass('nav-tabs');
                            }
                        }
                        if (this.view) this.view.render();
                    }
                }
            });

            // Responsive toolbar integration
            editor.Panels.addPanel({
                id: 'devices-panel',
                el: '.gjs-pn-devices-c',
                buttons: [
                    { id: 'device-desktop', command: (editor) => editor.setDevice('Desktop'), className: 'fa fa-desktop', active: 1 },
                    { id: 'device-tablet', command: (editor) => editor.setDevice('Tablet'), className: 'fa fa-tablet' },
                    { id: 'device-mobile', command: (editor) => editor.setDevice('Mobile'), className: 'fa fa-mobile' },
                ],
            });

            // Block Search Integration
            const initBlockSearch = () => {
                const blockManager = editor.BlockManager;
                const container = blockManager.getContainer();
                
                if (container && !document.getElementById('gx-block-search')) {
                    console.log('[Builder] Injecting Block Search UI...');
                    const searchWrapper = document.createElement('div');
                    searchWrapper.className = 'gx-block-search-container px-3 py-2';
                    searchWrapper.style.backgroundColor = 'rgba(0,0,0,0.15)';
                    searchWrapper.style.position = 'relative';
                    searchWrapper.style.zIndex = '10';
                    searchWrapper.innerHTML = `
                        <div class="position-relative">
                            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-2 text-white-50" style="font-size: 10px; opacity: 0.7;"></i>
                            <input type="text" id="gx-block-search" class="form-control form-control-sm bg-black bg-opacity-25 border-0 text-white rounded-pill ps-4 py-2" 
                                placeholder="Search blocks..." style="font-size: 11px; height: 28px; box-shadow: none; border: 1px solid rgba(255,255,255,0.03) !important;">
                        </div>
                    `;
                    
                    // GrapesJS categories container usually is the first child
                    container.prepend(searchWrapper);

                    const searchInput = searchWrapper.querySelector('#gx-block-search');
                    if (searchInput) {
                        searchInput.addEventListener('input', (e) => {
                        const val = e.target.value.toLowerCase();
                        const blocks = container.querySelectorAll('.gjs-block');
                        
                        if (val === '') {
                            // Reset everything to let GrapesJS handle open/close
                            blocks.forEach(b => b.style.display = '');
                            const categories = container.querySelectorAll('.gjs-block-category');
                            categories.forEach(cat => cat.style.display = '');
                            return;
                        }

                        blocks.forEach(block => {
                            const label = block.querySelector('.gjs-block-label');
                            const text = label ? label.textContent.toLowerCase() : '';
                            if (text.includes(val)) {
                                block.style.display = '';
                            } else {
                                block.style.display = 'none';
                            }
                        });

                        // Filter categories
                        const categories = container.querySelectorAll('.gjs-block-category');
                        categories.forEach(cat => {
                            const catBlocks = cat.querySelectorAll('.gjs-block');
                            let hasVisible = false;
                            catBlocks.forEach(b => {
                                if (b.style.display !== 'none') hasVisible = true;
                            });
                            cat.style.display = hasVisible ? '' : 'none';
                            
                            // Force open category if it has visible search results
                            if (hasVisible) {
                                cat.classList.add('gjs-open');
                                const content = cat.querySelector('.gjs-blocks-c');
                                if (content) content.style.display = '';
                            }
                        });
                    });
                }
            }
        };

            editor.on('load', () => {
                setTimeout(initBlockSearch, 100);
            });

            // Fallback for when the block manager tab is switched
            editor.on('run:open-blocks', () => {
                setTimeout(initBlockSearch, 50);
            });

            return editor;
        }

        function openBuilder() {
            const currentContent = getCurrentEditorContent();
            console.log('[Builder] openBuilder called. Content ready to inject length:', currentContent ? currentContent.length : 0);
            
            const modalElement = document.getElementById('builderModal');
            
            if (modalElement) {
                const modal = new window.bootstrap.Modal(modalElement);
                modal.show();

                modalElement.addEventListener('shown.bs.modal', function onShown() {
                    console.log('[Builder] Modal is fully shown. Initializing editor...');
                    
                    const injectContent = () => {
                        const data = currentContent;
                        if (data && data.html) {
                            console.log('[Builder] Preparing to set components and styles directly...');
                            
                            try {
                                editor.CssComposer.clear();
                                editor.DomComponents.clear();

                                console.log('[Builder] Setting CSS Style...');
                                if (data.css) {
                                    editor.setStyle(data.css);
                                }

                                console.log('[Builder] Setting HTML Components...');
                                editor.setComponents(data.html);

                                console.log('[Builder] Direct Injection Success.');
                            } catch (err) {
                                console.error('[Builder] Direct Injection Error:', err);
                            }
                        } else {
                            console.log('[Builder] No content to inject. Setting empty canvas.');
                            editor.DomComponents.clear();
                            editor.CssComposer.clear();
                        }
                    };

                    if (!editor) {
                        editor = createEditor();
                        initGridCreator(editor);
                        editor.on('load', () => {
                            console.log('[Builder] GrapesJS iframe loaded.');
                            setTimeout(() => {
                                injectContent();
                                editor.refresh();
                            }, 500); // Wait for components to register
                        });
                    } else {
                        console.log('[Builder] GrapesJS already running. Bypassing boot cycle.');
                        setTimeout(() => {
                            injectContent();
                            editor.refresh();
                        }, 300);
                    }
                    
                    modalElement.removeEventListener('shown.bs.modal', onShown);
                });
            } else {
                console.error('[Builder] Critical Error: modalElement #builderModal not found on page!');
            }
        }

        function initGridCreator(editor) {
            const toggle = document.getElementById('toggle-grid-picker');
            const picker = document.getElementById('grid-picker-box');
            const list = document.getElementById('grid-options-list');
            const closeBtn = document.getElementById('close-grid-picker');

            if (!toggle || !picker || !list) return;

            const gridOptions = [
                { label: 'Single Col', cols: ['col-12'], icon: '<div class="row g-1"><div class="col-12 border bg-primary opacity-50 p-1"></div></div>' },
                { label: '50 : 50', cols: ['col-md-6', 'col-md-6'], icon: '<div class="row g-1"><div class="col-6 border bg-primary opacity-50 p-1"></div><div class="col-6 border bg-primary opacity-50 p-1"></div></div>' },
                { label: '33 : 67', cols: ['col-md-4', 'col-md-8'], icon: '<div class="row g-1"><div class="col-4 border bg-primary opacity-50 p-1"></div><div class="col-8 border bg-primary opacity-50 p-1"></div></div>' },
                { label: '67 : 33', cols: ['col-md-8', 'col-md-4'], icon: '<div class="row g-1"><div class="col-8 border bg-primary opacity-50 p-1"></div><div class="col-4 border bg-primary opacity-50 p-1"></div></div>' },
                { label: '3 Columns', cols: ['col-md-4', 'col-md-4', 'col-md-4'], icon: '<div class="row g-1"><div class="col-4 border bg-primary opacity-50 p-1"></div><div class="col-4 border bg-primary opacity-50 p-1"></div><div class="col-4 border bg-primary opacity-50 p-1"></div></div>' },
                { label: '4 Columns', cols: ['col-md-3', 'col-md-3', 'col-md-3', 'col-md-3'], icon: '<div class="row g-1"><div class="col-3 border bg-primary opacity-50 p-1"></div><div class="col-3 border bg-primary opacity-50 p-1"></div><div class="col-3 border bg-primary opacity-50 p-1"></div><div class="col-3 border bg-primary opacity-50 p-1"></div></div>' },
                { label: '25:50:25', cols: ['col-md-3', 'col-md-6', 'col-md-3'], icon: '<div class="row g-1"><div class="col-3 border bg-primary opacity-50 p-1"></div><div class="col-6 border bg-primary opacity-50 p-1"></div><div class="col-3 border bg-primary opacity-50 p-1"></div></div>' }
            ];

            // Render options
            list.innerHTML = '';
            gridOptions.forEach(opt => {
                const col = document.createElement('div');
                col.className = 'col-3';
                col.innerHTML = `
                    <div class="grid-option p-2 bg-black bg-opacity-25 border border-white border-opacity-10 rounded-3 text-center cursor-pointer hover-bg-primary hover-border-primary" style="cursor: pointer; transition: all 0.2s;">
                        <div class="mb-2 text-primary opacity-75">${opt.icon}</div>
                        <span class="text-white-50" style="font-size: 9px; font-weight: bold;">${opt.label}</span>
                    </div>
                `;
                col.onclick = () => {
                    addGridToCanvas(opt.cols);
                    picker.classList.add('d-none');
                };
                list.appendChild(col);
            });

            function positionPicker() {
                const ref = document.querySelector('.gjs-frame-wrapper') 
                         || document.querySelector('.gjs-cv-canvas');
                if (!ref) return;
                const refRect = ref.getBoundingClientRect();
                const parentRect = picker.parentElement.getBoundingClientRect();
                const pickerWidth = picker.offsetWidth;
                const canvasCenter = (refRect.left + refRect.width / 2) - parentRect.left;
                picker.style.left = (canvasCenter - pickerWidth / 2) + 'px';
                picker.style.transform = '';
            }

            window.addEventListener('resize', () => {
                if (!picker.classList.contains('d-none')) positionPicker();
            });

            toggle.onclick = () => {
                const isHidden = picker.classList.contains('d-none');
                toggle.classList.toggle('is-open', isHidden);
                if (isHidden) {
                    picker.classList.remove('d-none');
                    picker.offsetHeight; // force reflow so offsetWidth is accurate
                    positionPicker();
                    requestAnimationFrame(() => picker.classList.add('visible'));
                } else {
                    picker.classList.remove('visible');
                    setTimeout(() => picker.classList.add('d-none'), 200);
                }
            };

            closeBtn.onclick = () => {
                picker.classList.remove('visible');
                toggle.classList.remove('is-open');
                setTimeout(() => picker.classList.add('d-none'), 200);
            };

            function addGridToCanvas(cols) {
                const components = cols.map(c => ({
                    type: 'column',
                    attributes: { class: c },
                    style: { padding: '15px' },
                    components: [{ content: '<div class="p-4 bg-light rounded-4 text-center text-secondary small builder-grid-placeholder"><i class="bi bi-plus-circle me-2"></i>Drop content here</div>' }]
                }));

                const wrapper = editor.getWrapper();
                const newRow = wrapper.append({
                    type: 'row',
                    attributes: { class: 'row' },
                    style: { },
                    components: components
                });

                // Select the newly added row
                if (newRow && newRow[0]) {
                    editor.select(newRow[0]);
                }

                // Scroll to bottom
                setTimeout(() => {
                    const frame = editor.Canvas.getFrameEl();
                    if (frame && frame.contentWindow) {
                        frame.contentWindow.scrollTo({ top: frame.contentWindow.document.body.scrollHeight, behavior: 'smooth' });
                    }
                }, 150);
            }
        }

        function bindEvents() {
            const launchButton = document.getElementById('launch-builder');
            const saveButton = document.getElementById('save-builder-page');

            if (launchButton) {
                launchButton.addEventListener('click', function () {
                    openBuilder();
                });
            }

            if (saveButton) {
                saveButton.addEventListener('click', function () {
                    if (!editor) return;

                    if (editor) {
                        // FORCE SYNC ALL CUSTOM CODE BLOCKS BEFORE EXPORT
                        const customComps = editor.DomComponents.getWrapper().find('.custom-code-container');
                        customComps.forEach(comp => {
                            const code = comp.get('data-gx-code');
                            if (code && code.indexOf('base64:') !== 0) {
                                comp.addAttributes({ 'data-gx-code': 'base64:' + utob(code) });
                            }
                        });

                        // BEFORE SYNC: Clean up preloader artifacts
                        const dynamicComps = editor.DomComponents.getWrapper().find('.recent-posts-container');
                        dynamicComps.forEach(comp => {
                            const el = comp.getEl();
                            if (el && (el.querySelector('.spinner-border') || el.innerHTML.includes('Connecting to Library'))) {
                                // Clear the component content in the GrapesJS Model
                                comp.set('content', ''); 
                                // Also ensure data-loaded is false so it re-triggers on frontend
                                comp.addAttributes({ 'data-loaded': 'false' });
                            }
                        });
                    }

                    const html = editor.getHtml();
                    const css = editor.getCss();
                    const js = editor.getJs();
                    exportContent(html, css, js);

                    if (window.toastr) window.toastr.success('Layout synchronized with main editor.');
                    
                    const modalElement = document.getElementById('builderModal');
                    if (modalElement) {
                        const modalInstance = window.bootstrap.Modal.getInstance(modalElement);
                        if (modalInstance) modalInstance.hide();
                    }
                });
            }
        }

        bindEvents();
    }

    document.addEventListener('DOMContentLoaded', function () {
        initDynamicContentLoader();
        initBuilder();
    });
})();
