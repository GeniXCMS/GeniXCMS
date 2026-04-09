(function () {
    if (typeof window.dynamicBuilderConfig === 'undefined') {
        window.dynamicBuilderConfig = {
            siteUrl: window.location.origin + '/',
            apiEndpoint: '/index.php?ajax=api&token=public&action=recent_posts&num=3',
            isSmartUrl: false,
        };
    }

    /**
     * Re-initialize all Bootstrap interactive components.
     * Builder content is injected AFTER Bootstrap's DOMContentLoaded fires,
     * so carousels and other interactive components need a manual re-init.
     */
    function reinitBootstrap() {
        if (typeof bootstrap === 'undefined') return;

        // Fix and Re-init Bootstrap Carousels
        document.querySelectorAll('.carousel').forEach(function(carousel, idx) {
            // 1. Ensure carousel has a unique ID
            var id = carousel.getAttribute('id');
            if (!id) {
                id = 'gx-carousel-' + idx + '-' + Math.random().toString(36).substr(2, 4);
                carousel.setAttribute('id', id);
            }
            // 2. Fix all indicator buttons
            carousel.querySelectorAll('[data-bs-slide-to]').forEach(function(btn) {
                btn.setAttribute('data-bs-target', '#' + id);
            });
            // 3. Fix prev/next control buttons
            carousel.querySelectorAll('[data-bs-slide]').forEach(function(btn) {
                btn.setAttribute('data-bs-target', '#' + id);
            });
            // 4. Use getOrCreateInstance to prevent double init
            try {
                bootstrap.Carousel.getOrCreateInstance(carousel, { ride: 'carousel', interval: 3000, wrap: true });
            } catch(e) {}
        });

        // Re-init Collapse (accordion) - with null checks
        document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(function(el) {
            if (el.__bs_collapse_init) return;
            var targetSel = el.getAttribute('data-bs-target');
            if (!targetSel) return;
            var targetEl = document.querySelector(targetSel);
            if (!targetEl) return;
            try { bootstrap.Collapse.getOrCreateInstance(targetEl, { toggle: false }); el.__bs_collapse_init = true; } catch(e) {}
        });

        // Re-init Dropdowns - with null check
        document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function(el) {
            if (el.__bs_dd_init) return;
            try { bootstrap.Dropdown.getOrCreateInstance(el); el.__bs_dd_init = true; } catch(e) {}
        });

        // Re-init Tooltips - with null check
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
            if (el.__bs_tt_init) return;
            try { bootstrap.Tooltip.getOrCreateInstance(el); el.__bs_tt_init = true; } catch(e) {}
        });

        // Re-init Tabs - with null check
        document.querySelectorAll('[data-bs-toggle="tab"]').forEach(function(el) {
            if (el.__bs_tab_init) return;
            try { new bootstrap.Tab(el); el.__bs_tab_init = true; } catch(e) {}
        });

        // ---- Re-init GX Custom Carousels ----
        initGxCarousels();
    }

    /**
     * Initialize custom GX carousel components (gx-img-carousel, gx-testi-carousel)
     * These use custom prev/next buttons with .gx-carousel-prev/.gx-carousel-next
     */
    function initGxCarousels() {
        // Helper to init a generic track-based carousel
        function setupCarousel(wrapper, trackSel, prevSel, nextSel, slideSel) {
            if (!wrapper || wrapper.__gx_carousel_init) return;
            var track = wrapper.querySelector(trackSel);
            var prevBtn = wrapper.querySelector(prevSel);
            var nextBtn = wrapper.querySelector(nextSel);
            if (!track || !prevBtn || !nextBtn) return;

            var currentIndex = 0;

            function getSlides() { return wrapper.querySelectorAll(slideSel); }
            function getVisibleCount() {
                var w = wrapper.offsetWidth;
                var slide = getSlides()[0];
                if (!slide) return 1;
                return Math.round(w / slide.offsetWidth) || 1;
            }
            function slideTo(idx) {
                var slides = getSlides();
                var visCount = getVisibleCount();
                var max = Math.max(0, slides.length - visCount);
                currentIndex = Math.min(Math.max(idx, 0), max);
                var offset = currentIndex * (slides[0] ? slides[0].offsetWidth + 24 : 0);
                track.style.transform = 'translateX(-' + offset + 'px)';
            }

            prevBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                slideTo(currentIndex - 1);
            });
            nextBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                slideTo(currentIndex + 1);
            });

            wrapper.__gx_carousel_init = true;
        }

        // Image Carousel
        document.querySelectorAll('.gx-img-carousel').forEach(function(el) {
            setupCarousel(el, '.gx-carousel-track', '.gx-carousel-prev', '.gx-carousel-next', '.gx-carousel-slide');
        });

        // Testimonial Carousel
        document.querySelectorAll('.gx-testi-carousel').forEach(function(el) {
            setupCarousel(el, '.gx-testi-track', '.gx-testi-prev', '.gx-testi-next', '.gx-testi-slide');
        });

        // Testimonial Slider (if different structure)
        document.querySelectorAll('.gx-testimonial-slider').forEach(function(el) {
            setupCarousel(el, '.gx-slider-track', '.gx-slider-prev', '.gx-slider-next', '.gx-slider-slide');
        });

        // Fix Bootstrap Carousel prev/next button data-bs-target if missing
        document.querySelectorAll('.carousel').forEach(function(carousel) {
            var id = carousel.getAttribute('id');
            if (!id) {
                id = 'gx-carousel-' + Math.random().toString(36).substr(2, 6);
                carousel.setAttribute('id', id);
            }
            carousel.querySelectorAll('[data-bs-slide]').forEach(function(btn) {
                if (!btn.getAttribute('data-bs-target')) {
                    btn.setAttribute('data-bs-target', '#' + id);
                }
            });
        });
    }

    function loadDynamicContent() {
        const containers = document.querySelectorAll('.recent-posts-container');
        if (!containers.length) return;

        containers.forEach(function (container) {
            if (container.getAttribute('data-loaded') === 'true') return;

            fetch(window.dynamicBuilderConfig.apiEndpoint)
                .then(function (response) { return response.json(); })
                .then(function (res) {
                    if (res.status === 'success' && Array.isArray(res.data) && res.data.length > 0) {
                        let html = '<div class="dynamic-posts-wrapper w-100"><div class="row g-3 text-dark text-start">';
                        res.data.forEach(function (post) {
                            html += '<div class="col-md-4">';
                            html += '<div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white">';
                            html += '<div class="ratio ratio-16x9"><a href="' + post.url + '"><img src="' + post.image + '" class="object-fit-cover w-100 h-100" alt="' + post.title + '"></a></div>';
                            html += '<div class="card-body p-3">';
                            html += '<div class="d-flex justify-content-between align-items-center mb-2">';
                            html += '<span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-pill" style="font-size: 9px; font-weight: 700;">' + (post.category || 'NEWS').toUpperCase() + '</span>';
                            html += '<small class="text-muted" style="font-size: 9px;">' + (post.date || '') + '</small>';
                            html += '</div>';
                            html += '<h6 class="fw-bold mb-2 lh-base"><a href="' + post.url + '" class="text-decoration-none text-dark" style="font-size: 14px;">' + post.title + '</a></h6>';
                            html += '<p class="text-muted small mb-0 opacity-75" style="font-size: 12px; line-height: 1.5;">' + (post.excerpt ? post.excerpt.substring(0, 80) + '...' : '') + '</p>';
                            html += '</div></div></div>';
                        });
                        html += '</div></div>';
                        container.innerHTML = html;
                        container.setAttribute('data-loaded', 'true');
                        container.style.display = 'block';
                    } else {
                        container.innerHTML = '<div class="alert alert-info py-2 small text-center">No CMS data found.</div>';
                    }
                })
                .catch(function () {
                    container.innerHTML = '<div class="alert alert-danger">Failed to load posts.</div>';
                });
        });
    }

    function init() {
        loadDynamicContent();
        // Reinit Bootstrap components after a short delay
        // to ensure all builder HTML has been injected into the DOM
        setTimeout(reinitBootstrap, 400);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Second pass on full page load for any late-loaded assets
    window.addEventListener('load', function() {
        loadDynamicContent();
        setTimeout(reinitBootstrap, 600);
    });
})();
