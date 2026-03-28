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
        <div class="card sidebar-card mb-5 border-0 shadow-sm overflow-hidden bg-white" data-aos="fade-left">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h3 class="card-title premium-sidebar-title fs-6 fw-bold text-uppercase m-0 d-flex align-items-center gap-2">
                    <span class="title-indicator"></span>
                    {_("Featured Stories")}
                </h3>
            </div>
            <div class="card-body p-4 pt-3">
                {var $recent = Posts::recent(['num' => 5])}
                {if !isset($recent['error'])}
                    {foreach $recent as $r}
                        {var $rp_img = Posts::getPostImage($r->id) ?: Gneex::getImage($r->content)}
                        <div class="sidebar-item d-flex align-items-center mb-4 transition-base">
                            <div class="flex-shrink-0">
                                <a href="{Url::post($r->id)}">
                                    <div class="sidebar-img-wrapper rounded-3 overflow-hidden shadow-sm" style="width: 70px; height: 70px;">
                                        <img src="{($rp_img != '') ? Url::thumb($rp_img, 'square', 100) : Url::theme() . 'assets/images/noimage.png'|noescape}" 
                                             class="w-100 h-100 object-fit-cover transition-base scale-on-hover" alt="{$r->title}">
                                    </div>
                                </a>
                            </div>
                            <div class="flex-grow-1 ms-3 overflow-hidden">
                                <h4 class="h6 mb-2 sidebar-post-title line-clamp-2">
                                    <a href="{Url::post($r->id)}" class="text-dark text-decoration-none fw-bold fs-7 lh-base">{$r->title}</a>
                                </h4>
                                <div class="extra-small text-muted opacity-75 d-flex align-items-center gap-2">
                                    <i class="fa fa-calendar-o text-primary"></i> {Date::format($r->date, 'd M Y')}
                                </div>
                            </div>
                        </div>
                    {/foreach}
                {/if}
            </div>
        </div>

        {* Recent Comments Widget *}
        <div class="card sidebar-card mb-5 border-0 shadow-sm overflow-hidden bg-white" data-aos="fade-left" data-aos-delay="100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h3 class="card-title premium-sidebar-title fs-6 fw-bold text-uppercase m-0 d-flex align-items-center gap-2">
                    <span class="title-indicator"></span>
                    {_("Discussion Center")}
                </h3>
            </div>
            <div class="card-body p-4 pt-3">
                {var $comments = Db::result("SELECT * FROM `comments` WHERE `status` = '1' AND `type` = 'post' ORDER BY `date` DESC LIMIT 5")}
                {if is_array($comments) && !isset($comments['error'])}
                    {foreach $comments as $c}
                        {var $c_author = !empty($c->userid) ? $c->userid : $c->name}
                        <div class="sidebar-comment-item mb-3 pb-3 border-bottom border-light-subtle last-child-border-0 transition-base">
                            <div class="extra-small fw-bold mb-2 text-dark d-flex align-items-center gap-2">
                                <div class="bg-primary rounded-circle" style="width: 6px; height: 6px;"></div>
                                {$c_author}
                            </div>
                            <div class="extra-small text-muted fst-italic ps-3 border-start border-primary border-opacity-10 ms-1 line-clamp-2">
                                "{$c->comment|stripHtml|truncate:85}"
                            </div>
                        </div>
                    {/foreach}
                {/if}
            </div>
        </div>

        {* Popular Tags Widget *}
        <div class="card sidebar-card mb-5 border-0 shadow-sm overflow-hidden bg-white" data-aos="fade-left" data-aos-delay="200">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h3 class="card-title premium-sidebar-title fs-6 fw-bold text-uppercase m-0 d-flex align-items-center gap-2">
                    <span class="title-indicator"></span>
                    {_("Theme Exploration")}
                </h3>
            </div>
            <div class="card-body p-4 pt-4">
                <div class="tag-discovery-cloud d-flex flex-wrap gap-2">
                    {Tags::cloud()|noescape}
                </div>
            </div>
        </div>
    {/if}
</aside>
