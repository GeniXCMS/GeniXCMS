<article class="blog-post p-0 overflow-hidden shadow-sm bg-white rounded-4 border-0 mb-4 h-100 d-flex flex-column" data-aos="fade-up">
    {var $ft_img = Posts::getPostImage($p->id)}
    {var $p_img = ($ft_img != "") ? $ft_img : Gneex::getImage($p->content)}
    {var $im_url = ($p_img != "") ? Url::thumb($p_img, 'large', '800') : Url::theme() . 'assets/images/noimage.png'}
    
    {if $p_img != ""}
        <div class="post-img overflow-hidden" style="height: 250px;">
            <a href="{Url::post($p->id)}">
                <img src="{$im_url|noescape}" class="img-fluid w-100 h-100 object-fit-cover transition-base d-block" alt="{$p->title}">
            </a>
        </div>
    {else}
        <div class="post-img overflow-hidden bg-light d-flex align-items-center justify-content-center" style="height: 250px;">
             <img src="{Url::thumb('assets/images/noimage.png', 'large', '800')}" class="img-fluid opacity-25" style="width: 120px;">
        </div>
    {/if}
    <div class="post-inner p-4 flex-grow-1 d-flex flex-column">
        <div class="post-meta mb-3 small opacity-75 d-flex align-items-center gap-3">
            <span><i class="fa fa-calendar-o me-1 text-primary"></i> {Date::format($p->date, 'd M Y')}</span>
            {if !empty($p->cat)}
                <span><i class="fa fa-folder-open-o me-1 text-primary"></i> <a href="{Url::cat($p->cat)}" class="text-muted text-decoration-none">{Categories::name($p->cat)}</a></span>
            {/if}
        </div>
        <h2 class="post-title h5 mt-0 mb-3"><a href="{Url::post($p->id)}" class="text-dark text-decoration-none fw-bold lh-base">{$p->title|truncate:100}</a></h2>
        <div class="entry-content mb-4 text-muted">
            {Typo::Xclean($p->content)|stripHtml|truncate:300}
        </div>
        <div class="mt-auto pt-3 border-top border-light-subtle d-flex justify-content-between align-items-center">
            <a href="{Url::post($p->id)}" class="btn-read-more text-decoration-none d-inline-block small fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 1px;">
                Read Article <i class="fa fa-chevron-right ms-2" style="font-size: 9px;"></i>
            </a>
            <div class="small opacity-50"><i class="fa fa-user-circle me-1"></i> {$p->author}</div>
        </div>
    </div>
</article>
<style>
/* Local clamp logic if not in CSS */
.line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
.blog-post:hover .transition-base { transform: scale(1.08); }
</style>
