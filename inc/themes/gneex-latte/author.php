<section id="blog" class="py-5 bg-light min-vh-100">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8 col-md-12">
                <div class="author-header mb-4 bg-white p-4 shadow-sm border-0 d-flex align-items-center">
                    <div class="author-avatar-large bg-primary text-white d-flex align-items-center justify-content-center fw-bold fs-3 me-4" style="width: 70px; height: 70px; border-radius: 50%;">
                        {substr($author, 0, 1)}
                    </div>
                    <div>
                        <h1 class="author-title fw-bold h3 m-0 text-dark">Articles by {$author}</h1>
                        <p class="text-muted small m-0 mt-1">Showing all stories published by this author</p>
                    </div>
                </div>

                <div class="blog-lists">
                    {if Gneex::opt('adsense')}
                        <div class="text-center mb-4">{Gneex::opt('adsense')|noescape}</div>
                    {/if}

                    {if $num > 0}
                        {foreach $posts as $p}
                            {var $p_content = Posts::content($p->content)}
                            {var $p_img = Gneex::getImage($p_content)}
                            
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
                                            {if $p->type != 'page' && $p->cat}
                                                <span class="ms-3 small text-muted"><i class="fa-solid fa-folder-open me-1"></i> {Categories::name($p->cat)}</span>
                                            {/if}
                                        </div>
                                        <h3 class="post-title h5 fw-bold mb-3"><a href="{Url::post($p->id)}" class="text-dark text-decoration-none">{$p->title|truncate:80}</a></h3>
                                        <div class="excerpt text-muted mb-3 small">
                                            {$p_content|stripHtml|truncate:120}
                                        </div>
                                        <a href="{Url::post($p->id)}" class="btn btn-read-more btn-sm py-2">Read More</a>
                                    </div>
                                </div>
                            </article>
                        {/foreach}
                        
                        <div class="pagination-wrapper mt-5 d-flex justify-content-center">
                            {$paging|noescape}
                        </div>
                    {else}
                        <div class="text-center py-5 bg-white shadow-sm">
                            <i class="fa-regular fa-circle-user fa-4x text-light mb-4"></i>
                            <h3 class="text-muted fw-bold">Author has no public posts</h3>
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
