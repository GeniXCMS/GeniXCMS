<section id="blog" class="py-5 bg-light min-vh-100">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8 col-md-12">
                <div class="tag-header mb-4 bg-white p-4 shadow-sm border-0">
                    <h1 class="tag-title fw-bold h3 m-0 text-dark">Tag: {$name}</h1>
                    <p class="text-muted small m-0 mt-2">Browsing all articles tagged with <span class="badge bg-primary">{$name}</span></p>
                </div>

                <div class="blog-lists">
                    {if Gneex::opt('adsense')}
                        <div class="text-center mb-4">{Gneex::opt('adsense')|noescape}</div>
                    {/if}

                    {if $num > 0}
                        {foreach $posts as $p}
                            {var $p_content = Posts::content($p->content)}
                            {var $p_img = Gneex::getImage($p_content, $p->id)}
                            
                            <article class="blog-post blog-post-card p-0 overflow-hidden shadow-sm bg-white rounded-4 border-0 mb-4" data-aos="fade-up">
                                <div class="row g-0 align-items-center">
                                    {if $p_img}
                                        <div class="col-md-4">
                                            <a href="{Url::post($p->id)}">
                                                <div class="post-img overflow-hidden" style="min-height: 200px; height: 100%;">
                                                    <img src="{Url::thumb($p_img, 'large', '600')}" class="img-fluid w-100 h-100 object-fit-cover transition-base" alt="{$p->title}">
                                                </div>
                                            </a>
                                        </div>
                                    {/if}
                                    <div class="col-md-{$p_img ? '8' : '12'} p-4">
                                        <div class="post-meta mb-2 small opacity-50">
                                            <span class="text-primary fw-bold"><i class="fa-regular fa-calendar me-1"></i> {Date::format($p->date, 'd M Y')}</span>
                                        </div>
                                        <h3 class="post-title h5 fw-bold mb-2"><a href="{Url::post($p->id)}" class="text-dark text-decoration-none lh-base">{$p->title}</a></h3>
                                        <div class="excerpt text-muted mb-3 extra-small lh-lg">
                                            {$p_content|stripHtml|truncate:180}
                                        </div>
                                        <a href="{Url::post($p->id)}" class="text-primary text-decoration-none small fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 1px;">Read More <i class="fa fa-chevron-right ms-1" style="font-size: 9px;"></i></a>
                                    </div>
                                </div>
                            </article>
                        {/foreach}
                        
                        <div class="pagination-wrapper mt-5 d-flex justify-content-center">
                            {$paging|noescape}
                        </div>
                    {else}
                        <div class="text-center py-5 bg-white shadow-sm">
                            <i class="fa-solid fa-tag fa-4x text-light mb-4"></i>
                            <h3 class="text-muted fw-bold">No articles found with this tag</h3>
                        </div>
                    {/if}
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                {include 'rightside.php'}
            </div>
        </div>
    </div>
</section>
