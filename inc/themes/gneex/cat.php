<section id="blog" class="py-5 bg-light min-vh-100">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8 col-md-12">
                <div class="category-header mb-4">
                    <h2 class="fw-extrabold text-dark d-flex align-items-center">
                        <span class="bg-primary p-1 me-2" style="width: 8px; height: 2rem"></span>
                        {Categories::name($cat)}
                    </h2>
                </div>
                <div class="blog-lists">
                    {if Gneex::opt('adsense')}
                        <div class="text-center mb-4">{Gneex::opt('adsense')|noescape}</div>
                    {/if}

                    {if $posts}
                        {if Gneex::opt('category_layout') == 'magazine'}
                            <div class="row g-4">
                                {foreach $posts as $p}
                                    {var $p_content = Posts::content($p->content)}
                                    {var $p_img = Gneex::getImage($p_content, $p->id)}
                                    <div class="col-sm-6 col-md-4" data-aos="fade-up" data-aos-delay="{$iterator->counter * 100}">
                                        <article class="blog-post blog-post-card p-0 overflow-hidden shadow-sm bg-white rounded-4 border-0 h-100 d-flex flex-column">
                                            <a href="{Url::post($p->id)}">
                                                <div class="post-img overflow-hidden" style="height: 180px;">
                                                    <img src="{$p_img ? Url::thumb($p_img, 'large', '400') : Url::thumb(Url::theme() . 'images/noimage.png', 'large', '400')}" class="img-fluid w-100 h-100 object-fit-cover transition-base" alt="{$p->title}">
                                                </div>
                                            </a>
                                            <div class="post-inner p-4 flex-grow-1 d-flex flex-column">
                                                <div class="post-meta mb-2 small opacity-50">
                                                    <span><i class="fa-regular fa-calendar me-1"></i> {Date::format($p->date, 'd M Y')}</span>
                                                </div>
                                                <h4 class="post-title h6 fw-bold mb-3">
                                                    <a href="{Url::post($p->id)}" class="text-dark text-decoration-none lh-base">{$p->title|truncate:80}</a>
                                                </h4>
                                                <div class="entry-content mb-3 text-muted small extra-small lh-base">
                                                    {$p_content|stripHtml|truncate:100}
                                                </div>
                                                <div class="mt-auto pt-2 border-top border-light-subtle">
                                                    <a href="{Url::post($p->id)}" class="text-primary text-decoration-none extra-small fw-bold text-uppercase" style="font-size: 10px; letter-spacing: 0.5px;">
                                                        Read More <i class="fa fa-arrow-right ms-1" style="font-size: 8px;"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </article>
                                    </div>
                                {/foreach}
                            </div>
                        {else}
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
                        {/if}
                        
                        <div class="pagination-wrapper mt-5 d-flex justify-content-center">
                            {$paging|noescape}
                        </div>
                    {else}
                        <div class="text-center py-5 bg-white shadow-sm">
                            <i class="fa-solid fa-folder-open fa-4x text-light mb-4"></i>
                            <h3 class="text-muted fw-bold">No Post in this Category</h3>
                        </div>
                    {/if}

                    {if Gneex::opt('adsense')}
                        <div class="text-center mt-4">{Gneex::opt('adsense')|noescape}</div>
                    {/if}
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                {include 'rightside.php'}
            </div>
        </div>
    </div>
</section>
