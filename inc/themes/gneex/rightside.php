{if Gneex::opt('adsense')}
    <div class="row mb-4"><div class="col-md-12 text-center">{Gneex::opt('adsense')|noescape}</div></div>
{/if}

<aside class="sidebar-portal">
    {* Handle Dynamic Widgets if any *}
    {if class_exists('Widget')}
        {Widget::show('sidebar')|noescape}
    {/if}

    {* Fallback to premium default widgets if no dynamic widgets active *}
    {if !Db::result("SELECT * FROM `widgets` WHERE `status` = '1' AND `location` = 'sidebar'")}
        {* Recent Posts Widget *}
        <div class="card sidebar-card mb-5 border-0 shadow-sm overflow-hidden" data-aos="fade-left">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h3 class="card-title fs-6 fw-900 text-uppercase letter-spacing-1 m-0">Featured Highlights</h3>
            </div>
            <div class="card-body p-4 pt-0">
                {var $recent = Posts::recent(['num' => 5])}
                {if !isset($recent['error'])}
                    {foreach $recent as $r}
                        {var $rp_img = Posts::getPostImage($r->id) ?: Gneex::getImage($r->content)}
                        <div class="sidebar-item d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <a href="{Url::post($r->id)}">
                                    <div class="sidebar-img-wrapper rounded-3 overflow-hidden shadow-sm" style="width: 65px; height: 65px;">
                                        <img src="{($rp_img != '') ? Url::thumb($rp_img, 'square', 100) : Url::theme() . 'assets/images/noimage.png'|noescape}" 
                                             class="w-100 h-100 object-fit-cover transition-base" alt="{$r->title}">
                                    </div>
                                </a>
                            </div>
                            <div class="flex-grow-1 ms-3 overflow-hidden">
                                <h4 class="h6 mb-1 sidebar-post-title">
                                    <a href="{Url::post($r->id)}" class="text-dark text-decoration-none fw-bold small d-block text-truncate">{$r->title}</a>
                                </h4>
                                <div class="small opacity-50"><i class="fa fa-clock-o me-1"></i> {Date::format($r->date, 'd M Y')}</div>
                            </div>
                        </div>
                    {/foreach}
                {/if}
            </div>
        </div>

        {* Recent Comments Widget *}
        <div class="card sidebar-card mb-5 border-0 shadow-sm overflow-hidden" data-aos="fade-left" data-aos-delay="100">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h3 class="card-title fs-6 fw-900 text-uppercase letter-spacing-1 m-0">Discussion Center</h3>
            </div>
            <div class="card-body p-4 pt-0">
                {var $comments = Db::result("SELECT * FROM `comments` WHERE `status` = '1' AND `type` = 'post' ORDER BY `date` DESC LIMIT 5")}
                {if is_array($comments) && !isset($comments['error'])}
                    {foreach $comments as $c}
                        {var $c_author = !empty($c->userid) ? $c->userid : $c->name}
                        <div class="sidebar-comment-item mb-3 pb-3 border-bottom border-light-subtle last-child-border-0">
                            <div class="small fw-bold mb-1"><i class="fa fa-commenting-o text-primary me-2"></i> {$c_author}</div>
                            <div class="small text-muted line-clamp-2 italic ps-4">"{$c->comment|stripHtml|truncate:80}"</div>
                        </div>
                    {/foreach}
                {/if}
            </div>
        </div>

        {* Popular Tags Widget *}
        <div class="card sidebar-card mb-5 border-0 shadow-sm overflow-hidden" data-aos="fade-left" data-aos-delay="200">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h3 class="card-title fs-6 fw-900 text-uppercase letter-spacing-1 m-0">Topic Discovery</h3>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="tag-discovery-cloud d-flex flex-wrap gap-2">
                    {Tags::cloud()|noescape}
                </div>
            </div>
        </div>
    {/if}
</aside>

<style>
.sidebar-post-title a:hover { color: var(--primary-color) !important; }
.sidebar-item:hover .transition-base { transform: scale(1.1); }
.tag-discovery-cloud a { 
    font-size: 11px !important; 
    text-transform: uppercase; 
    letter-spacing: 0.5px; 
    font-weight: 700; 
    background: #f8fafc; 
    color: #475569; 
    padding: 6px 14px; 
    border-radius: 50px; 
    text-decoration: none; 
    border: 1px solid #f1f5f9;
    transition: var(--transition);
}
.tag-discovery-cloud a:hover { 
    background: var(--primary-color); 
    color: #fff; 
    border-color: var(--primary-color);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.last-child-border-0:last-child { border-bottom: 0 !important; }
</style>
