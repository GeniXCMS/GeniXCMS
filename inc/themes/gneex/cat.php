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
                                    <div class="col-sm-6" data-aos="fade-up" data-aos-delay="{$iterator->counter * 100}">
                                        <div class="card h-100 border-0 shadow-sm overflow-hidden">
                                            <a href="{Url::post($p->id)}">
                                                <img src="{$p_img ? Url::thumb($p_img, 'large') : Url::thumb('assets/images/noimage.png', 'large')}" class="card-img-top" style="height: 200px; object-fit: cover;">
                                            </a>
                                            <div class="card-body p-3">
                                                <div class="post-meta mb-2 small text-muted">
                                                    <span><i class="fa-regular fa-calendar me-1"></i> {Date::format($p->date, 'd M Y')}</span>
                                                </div>
                                                <h4 class="card-title h6 fw-bold mb-2 pt-1 border-top">
                                                    <a href="{Url::post($p->id)}" class="text-dark text-decoration-none">{$p->title}</a>
                                                </h4>
                                                <p class="card-text small text-muted">
                                                    {$p_content|stripHtml|truncate:80}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                {/foreach}
                            </div>
                        {else}
                            {foreach $posts as $p}
                                {var $p_content = Posts::content($p->content)}
                                {var $p_img = Gneex::getImage($p_content, $p->id)}
                                
                                <article class="blog-post p-0 overflow-hidden shadow-sm bg-white border-0 mb-4" data-aos="fade-up">
                                    <div class="row g-0 align-items-center">
                                        {if $p_img}
                                            <div class="col-md-5">
                                                <a href="{Url::post($p->id)}">
                                                    <img src="{Url::thumb($p_img, 'large')}" class="img-fluid h-100" style="object-fit: cover; min-height: 200px;">
                                                </a>
                                            </div>
                                        {/if}
                                        <div class="col-md-{$p_img ? '7' : '12'} p-4">
                                            <div class="post-meta mb-2">
                                                <span class="text-primary small fw-bold"><i class="fa-regular fa-calendar me-1"></i> {Date::format($p->date, 'd M Y')}</span>
                                            </div>
                                            <h3 class="post-title h5 fw-bold mb-3"><a href="{Url::post($p->id)}" class="text-dark text-decoration-none">{$p->title}</a></h3>
                                            <div class="excerpt text-muted mb-3 small">
                                                {$p_content|stripHtml|truncate:120}
                                            </div>
                                            <a href="{Url::post($p->id)}" class="btn btn-read-more btn-sm py-2">Read More</a>
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
