<section id="blog" class="py-5 bg-light min-vh-100">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8 col-md-12">
                <div class="archive-header mb-5 p-5 bg-white rounded-5 shadow-sm border-0 position-relative overflow-hidden" data-aos="fade-down">
                    <div class="position-absolute top-0 start-0 w-100 h-100 opacity-05 bg-primary" style="background: linear-gradient(135deg, rgba(var(--primary-rgb), 0.1) 0%, rgba(var(--primary-rgb), 0.02) 100%);"></div>
                    <div class="position-relative z-index-1">
                        <h6 class="text-primary fw-bold text-uppercase mb-3 tracking-widest" style="font-size: 11px; letter-spacing: 2px;">
                           <i class="fa fa-calendar-days me-2"></i> Monthly Archive
                        </h6>
                        <h2 class="display-4 fw-black text-dark mb-2 tracking-tight">{$data['dateName'] ?? 'Archive'}</h2>
                        <p class="text-muted mb-0 fs-5 opacity-75">{_('Browsing our history. All articles from this period.')}</p>
                    </div>
                </div>

                <div class="blog-lists">
                    {if Gneex::opt('adsense')}
                        <div class="text-center mb-4">{Gneex::opt('adsense')|noescape}</div>
                    {/if}

                    {if $posts}
                        <div class="row g-4">
                            {foreach $posts as $p}
                                {var $p_content = Posts::content($p->content)}
                                {var $p_img = Gneex::getImage($p_content, $p->id)}
                                <div class="col-md-6" data-aos="fade-up" data-aos-delay="{$iterator->counter * 50}">
                                    <article class="blog-post blog-post-card p-0 overflow-hidden shadow-sm bg-white rounded-4 border-0 h-100 d-flex flex-column transition-base hover-shadow-lg">
                                        <a href="{Url::post($p->id)}">
                                            <div class="post-img overflow-hidden position-relative" style="height: 220px;">
                                                <img src="{$p_img ? Url::thumb($p_img, 'large', '400') : Url::thumb(Url::theme() . 'images/noimage.png', 'large', '400')}" class="img-fluid w-100 h-100 object-fit-cover transition-base scale-on-hover" alt="{$p->title}">
                                                <div class="position-absolute top-0 end-0 p-3">
                                                    <span class="badge bg-white text-dark shadow-sm rounded-pill px-3 py-2 fw-bold" style="font-size: 10px;">
                                                       <i class="fa fa-calendar me-1"></i> {Date::format($p->date, 'd M Y')}
                                                    </span>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="post-inner p-4 flex-grow-1 d-flex flex-column">
                                            <h4 class="post-title h5 fw-bold mb-3">
                                                <a href="{Url::post($p->id)}" class="text-dark text-decoration-none lh-base hover-text-primary">{$p->title|truncate:80}</a>
                                            </h4>
                                            <div class="entry-content mb-4 text-muted small extra-small lh-base opacity-75">
                                                {$p_content|stripHtml|truncate:120}
                                            </div>
                                            <div class="mt-auto pt-3 border-top border-light-subtle d-flex justify-content-between align-items-center">
                                                <a href="{Url::post($p->id)}" class="text-primary text-decoration-none extra-small fw-bold text-uppercase tracking-wider" style="font-size: 10px;">
                                                    Read More <i class="fa fa-arrow-right ms-1"></i>
                                                </a>
                                                <span class="text-muted extra-small" style="font-size: 10px;">
                                                   <i class="fa fa-user me-1"></i> {$p->author}
                                                </span>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            {/foreach}
                        </div>
                        
                        <div class="pagination-wrapper mt-5 d-flex justify-content-center">
                            {$paging|noescape}
                        </div>
                    {else}
                        <div class="text-center py-5 bg-white shadow-sm rounded-4">
                            <i class="fa-solid fa-calendar-xmark fa-4x text-light mb-4 opacity-25"></i>
                            <h3 class="text-muted fw-bold">No Post in this Archive</h3>
                            <p class="text-muted mb-0">Try searching for something else or browse popular tags.</p>
                        </div>
                    {/if}

                    {if Gneex::opt('adsense')}
                        <div class="text-center mt-4">{Gneex::opt('adsense')|noescape}</div>
                    {/if}
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="sidebar-portal sticky-top" style="top: 100px;">
                    {include 'rightside.php'}
                </div>
            </div>
        </div>
    </div>
</section>
