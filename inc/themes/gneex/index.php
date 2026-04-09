
{var $opt = Gneex::$opt}
{var $front_layout = Gneex::opt('front_layout')}

{if Gneex::featuredExist() && ($front_layout != 'fullwidth' || ($p_type ?? '') != 'index' || (isset($curr_paging) && $curr_paging > 1))}
    {var $feat_bg_color = Gneex::opt('background_color_featured') ?: '#ffffff'}
    {var $feat_bg_img = Gneex::opt('background_featured')}
    {var $feat_style = "background-color: $feat_bg_color !important;"}
    {if $feat_bg_img}
        {var $feat_style = $feat_style . " background-image: url('$feat_bg_img'); background-size: cover; background-position: center;"}
    {/if}
    <section id="featured" class="py-5" style="{$feat_style|noescape}">
        <div class="container">
            <div class="section-title-wrapper mb-4 d-flex align-items-center justify-content-between">
                <div>
                    <h2 class="fw-bold text-dark m-0"><?=_("Featured Stories");?></h2>
                    <p class="text-muted small mb-0"><?=_("Our handpicked recommendations for you.");?></p>
                </div>
            </div>
            
            <div class="row g-4">
                {var $feat = explode(',', $opt['featured_posts'])}
                {foreach $feat as $id}
                    {var $p = Db::result("SELECT * FROM `posts` WHERE `id` = ? LIMIT 1", [$id])}
                    {if !isset($p['error'])}
                        {var $p = $p[0]}
                        {var $p_img = Gneex::getImage($p->content, $p->id)}
                        {var $im_url = $p_img ? Url::thumb($p_img, 'large', 500) : Url::thumb(Url::theme() . 'images/noimage.png', 'large')}
                        <div class="col-lg-3 col-md-6">
                            <div class="feat-card-premium border-0 overflow-hidden shadow-sm h-100 rounded-4 position-relative">
                                <a href="{Url::post($p->id)}" class="text-decoration-none h-100 d-block">
                                    <div class="feat-card-img-container h-100" style="min-height: 380px;">
                                        <img src="{$im_url}" class="card-img h-100 w-100 object-fit-cover transition-base" alt="{$p->title}">
                                        <div class="feat-card-overlay position-absolute bottom-0 start-0 end-0 top-0 d-flex flex-column justify-content-end p-4">
                                            <h3 class="h5 fw-bold text-white mb-0 lh-base text-shadow">{$p->title|truncate:60}</h3>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    {/if}
                    {breakIf $iterator->counter == 4}
                {/foreach}
            </div>
        </div>
    </section>
{/if}

{if $front_layout == 'fullwidth' && ($p_type ?? '') == 'index' && (!isset($curr_paging) || $curr_paging == 1)}
    <section id="fullwidth-frontpage-content" class="w-100 p-0 m-0 overflow-hidden" style="min-height: 100vh;">
        {var $fw_id = Gneex::opt('fullwidth_page')}
        {if $fw_id}
            {Gneex::getPost($fw_id)|noescape}
        {else}
            <div class="container py-5 text-center">
                <div class="alert alert-warning rounded-4 shadow-sm border-0 p-5">
                    <h2 class="fw-bold">No Page Selected</h2>
                    <p class="lead text-muted mb-4">Please select a page in Theme Options -> Front Page Settings.</p>
                </div>
            </div>
        {/if}
    </section>
{else}
    <section id="blog" class="py-5 bg-light min-vh-100">
        <div class="container">
            <div class="row g-4">
            <div class="col-lg-8 col-md-12">
                {if Gneex::opt('front_layout') == 'magazine'}

                    {* -- Dynamic Google Fonts loader for panel fonts -- *}
                    {Gneex::panelFontsLink()|noescape}

                    {* Panel 1 *}
                    {var $cat1 = $opt['panel_1']}
                    {if $cat1}
                        {var $cat1_posts = Posts::getPostByCat($cat1, 8)}
                        {var $p1_ff = $opt['panel_1_font_family'] ?: 'inherit'}
                        {var $p1_fs = ($opt['panel_1_font_size'] ?: '1').'rem'}
                        {var $p1_style = 'background:'.($opt['panel_1_bg'] ?: 'transparent').';color:'.($opt['panel_1_text_color'] ?: 'inherit').';font-family:'.($opt['panel_1_font_family'] ?: 'inherit').';font-size:'.($opt['panel_1_font_size'] ?: '1').'rem;'}
                        <div class="card panel panel-one mb-4 shadow-sm" data-aos="fade-up" style="{$p1_style|noescape}">
                            <div class="card-header panel-heading border-bottom" style="background:{$opt['panel_1_color'] ?: 'var(--primary-color)'|noescape};color:{$opt['panel_1_font_color'] ?: 'white'|noescape};">
                                <h3 class="card-title panel-title m-0 fw-bold">{Categories::name($cat1)}</h3>
                            </div>
                            <div class="card-body panel-body">
                                <div class="row">
                                    {if !isset($cat1_posts['error'])}
                                        {var $first = $cat1_posts[0]}
                                        {var $first_img = Gneex::getImage($first->content, $first->id)}
                                        <div class="col-sm-5">
                                            <a href="{Url::post($first->id)}" class="text-decoration-none" style="font-family:{$p1_ff|noescape};">
                                                <div class="horizontal-list mb-3">
                                                    <img src="{$first_img ? Url::thumb($first_img, 'large') : Url::thumb('assets/images/noimage.png', 'large')}" class="img-fluid shadow-sm">
                                                </div>
                                                <div>
                                                    <h4 class="fw-bold fs-5" style="color:{$opt['panel_1_font_color'] ?: 'inherit'|noescape};font-family:{$p1_ff|noescape};">{$first->title}</h4>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-sm-7">
                                            <ul class="list-unstyled">
                                                {foreach $cat1_posts as $p}
                                                    {continueIf $iterator->first}
                                                    <li class="mb-2 pb-2 border-bottom">
                                                        <h5 class="m-0"><a href="{Url::post($p->id)}" class="text-decoration-none small fw-bold" style="color:{$opt['panel_1_text_color'] ?: 'var(--bs-dark)'|noescape};font-family:{$p1_ff|noescape};font-size:{$p1_fs|noescape};">{$p->title}</a></h5>
                                                    </li>
                                                {/foreach}
                                            </ul>
                                        </div>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    {/if}

                    {if Gneex::opt('adsense')}
                        <div class="row mb-4"><div class="col-md-12 text-center">{Gneex::opt('adsense')|noescape}</div></div>
                    {/if}

                    <div class="row">
                        {* Panel 2 & 3 *}
                        {foreach [2, 3] as $i}
                            {var $cat = $opt['panel_'.$i]}
                            {if $cat}
                                {var $col = (!empty($opt['panel_2']) && !empty($opt['panel_3'])) ? '6' : '12'}
                                {var $cat_posts = Posts::getPostByCat($cat, 6)}
                                {var $px_ff = $opt['panel_'.$i.'_font_family'] ?: 'inherit'}
                                {var $px_fs = ($opt['panel_'.$i.'_font_size'] ?: '1').'rem'}
                                {var $px_style = 'background:'.($opt['panel_'.$i.'_bg'] ?: 'transparent').';color:'.($opt['panel_'.$i.'_text_color'] ?: 'inherit').';font-family:'.($opt['panel_'.$i.'_font_family'] ?: 'inherit').';font-size:'.($opt['panel_'.$i.'_font_size'] ?: '1').'rem;'}
                                <div class="col-sm-{$col} mb-4" data-aos="fade-up" data-aos-delay="{$iterator->counter * 100}">
                                    <div class="card panel panel-{($i == 2 ? 'two' : 'three')} h-100 shadow-sm" style="{$px_style|noescape}">
                                        <div class="card-header panel-heading border-bottom" style="background:{$opt['panel_'.$i.'_color'] ?: 'var(--primary-color)'|noescape};color:{$opt['panel_'.$i.'_font_color'] ?: 'white'|noescape};">
                                            <h3 class="card-title panel-title m-0 fw-bold">{Categories::name($cat)}</h3>
                                        </div>
                                        <div class="card-body panel-body">
                                            {if !isset($cat_posts['error'])}
                                                {var $first = $cat_posts[0]}
                                                {var $first_img = Gneex::getImage($first->content, $first->id)}
                                                <div class="vertical-list mb-3">
                                                    <img src="{$first_img ? Url::thumb($first_img, 'large') : Url::thumb('assets/images/noimage.png', 'large')}" class="img-fluid shadow-sm">
                                                </div>
                                                <h4 class="fw-bold fs-5"><a href="{Url::post($first->id)}" class="text-decoration-none" style="color:{$opt['panel_'.$i.'_font_color'] ?: 'var(--bs-dark)'|noescape};font-family:{$px_ff|noescape};font-size:{$px_fs|noescape};">{$first->title}</a></h4>
                                                <ul class="list-unstyled mt-3">
                                                    {foreach $cat_posts as $p}
                                                        {continueIf $iterator->first}
                                                        <li class="mb-2">
                                                            <h5 class="m-0"><a href="{Url::post($p->id)}" class="text-decoration-none small" style="color:{$opt['panel_'.$i.'_text_color'] ?: 'var(--bs-secondary)'|noescape};font-family:{$px_ff|noescape};font-size:{$px_fs|noescape};">{$p->title}</a></h5>
                                                        </li>
                                                    {/foreach}
                                                </ul>
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                            {/if}
                        {/foreach}
                    </div>

                    {* Panel 4 *}
                    {var $cat4 = $opt['panel_4']}
                    {if $cat4}
                        {var $cat4_posts = Posts::getPostByCat($cat4, 4)}
                        {if !isset($cat4_posts['error'])}
                        {var $p4_ff = $opt['panel_4_font_family'] ?: 'inherit'}
                        {var $p4_fs = ($opt['panel_4_font_size'] ?: '1').'rem'}
                        {var $p4_style = 'color:'.($opt['panel_4_text_color'] ?: 'inherit').';font-family:'.($opt['panel_4_font_family'] ?: 'inherit').';font-size:'.($opt['panel_4_font_size'] ?: '1').'rem;'}
                        <div class="panel-four-wrapper mb-4" data-aos="fade-up" style="background:{$opt['panel_4_bg'] ?: 'transparent'|noescape};padding:{$opt['panel_4_bg'] ? '16px' : '0'};border-radius:{$opt['panel_4_bg'] ? '12px' : '0'};">
                            <h3 class="fw-bold mb-3 d-flex align-items-center" style="{$p4_style|noescape}">
                                <span class="p-1 me-2" style="width: 10px; height: 1.5rem; background-color: {$opt['panel_4_color'] ?: 'var(--primary-color)'|noescape}"></span>
                                {Categories::name($cat4)}
                            </h3>
                            <div class="row g-3">
                                {foreach $cat4_posts as $p}
                                    <div class="col-sm-3">
                                        <div class="card h-100 border-0 shadow-sm overflow-hidden">
                                            {var $p_img = Gneex::getImage($p->content, $p->id)}
                                            <a href="{Url::post($p->id)}">
                                                <img src="{$p_img ? Url::thumb($p_img, 'large') : Url::thumb('assets/images/noimage.png', 'large')}" class="card-img-top" style="height: 120px; object-fit: cover;">
                                            </a>
                                            <div class="p-2" style="background:{$opt['panel_4_bg'] ?: 'transparent'|noescape};">
                                                <h6 class="m-0 small fw-bold">
                                                    <a href="{Url::post($p->id)}" class="text-decoration-none" style="color:{$opt['panel_4_text_color'] ?: 'var(--bs-dark)'|noescape};font-family:{$p4_ff|noescape};font-size:{$p4_fs|noescape};">{$p->title|truncate:50}</a>
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                {/foreach}
                            </div>
                        </div>
                        {/if}
                    {/if}

                    {* Panel 5 *}
                    {var $cat5 = $opt['panel_5']}
                    {if $cat5}
                        {var $cat5_posts = Posts::getPostByCat($cat5, 8)}
                        {var $p5_ff = $opt['panel_5_font_family'] ?: 'inherit'}
                        {var $p5_fs = ($opt['panel_5_font_size'] ?: '1').'rem'}
                        {var $p5_style = 'background:'.($opt['panel_5_bg'] ?: 'transparent').';color:'.($opt['panel_5_text_color'] ?: 'inherit').';font-family:'.($opt['panel_5_font_family'] ?: 'inherit').';font-size:'.($opt['panel_5_font_size'] ?: '1').'rem;'}
                        <div class="card panel panel-five mb-4 shadow-sm" data-aos="fade-up" style="{$p5_style|noescape}">
                            <div class="card-header panel-heading border-bottom text-end" style="background:{$opt['panel_5_color'] ?: 'var(--primary-color)'|noescape};color:{$opt['panel_5_font_color'] ?: 'white'|noescape};">
                                <h3 class="card-title panel-title m-0 fw-bold">{Categories::name($cat5)}</h3>
                            </div>
                            <div class="card-body panel-body">
                                <div class="row">
                                    {if !isset($cat5_posts['error'])}
                                        {var $first = $cat5_posts[0]}
                                        {var $first_img = Gneex::getImage($first->content, $first->id)}
                                        <div class="col-sm-7 order-2 order-sm-1">
                                            <ul class="list-unstyled">
                                                {foreach $cat5_posts as $p}
                                                    {continueIf $iterator->first}
                                                    <li class="mb-2 pb-2 border-bottom">
                                                        <h5 class="m-0"><a href="{Url::post($p->id)}" class="text-decoration-none small fw-bold" style="color:{$opt['panel_5_text_color'] ?: 'var(--bs-dark)'|noescape};font-family:{$p5_ff|noescape};font-size:{$p5_fs|noescape};">{$p->title}</a></h5>
                                                    </li>
                                                {/foreach}
                                            </ul>
                                        </div>
                                        <div class="col-sm-5 order-1 order-sm-2 text-end">
                                            <a href="{Url::post($first->id)}" class="text-decoration-none" style="font-family:{$p5_ff|noescape};">
                                                <div class="horizontal-list mb-3">
                                                    <img src="{$first_img ? Url::thumb($first_img, 'large') : Url::thumb('assets/images/noimage.png', 'large')}" class="img-fluid shadow-sm">
                                                </div>
                                                <div>
                                                    <h4 class="fw-bold fs-5" style="color:{$opt['panel_5_font_color'] ?: 'inherit'|noescape};font-family:{$p5_ff|noescape};">{$first->title}</h4>
                                                </div>
                                            </a>
                                        </div>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    {/if}

                    {* Fallback: Latest Posts if no panels matched *}
                    {if !Gneex::opt('panel_1') && !Gneex::opt('panel_2') && !Gneex::opt('panel_3') && !Gneex::opt('panel_4') && !Gneex::opt('panel_5')}
                        <div class="blog-lists row g-4">
                            {if $posts}
                                {foreach $posts as $p}
                                    <div class="col-md-6 mb-4">
                                        {include 'posts_loop.php', p => $p}
                                    </div>
                                {/foreach}
                            {/if}
                        </div>
                    {/if}

                {else}
                    <div class="blog-lists">
                        {if $posts}
                            {foreach $posts as $p}
                                <article class="blog-post p-0 overflow-hidden shadow-sm bg-white border-0 mb-4">
                                    {var $p_img = Gneex::getImage($p->content, $p->id)}
                                    {if $p_img}
                                        <div class="post-img">
                                            <a href="{Url::post($p->id)}">
                                                <img src="{Url::thumb($p_img, 'large')}" class="img-fluid w-100" style="max-height: 400px; object-fit: cover;">
                                            </a>
                                        </div>
                                    {/if}
                                    <div class="post-inner">
                                        <div class="post-meta">
                                            <span><i class="fa fa-calendar-o me-1 text-primary"></i> {Date::format($p->date, 'd M Y')}</span>
                                            {if $p->cat}<span><i class="fa fa-folder-open-o me-1 text-primary"></i> <a href="{Url::cat($p->cat)}" class="text-muted text-decoration-none">{Categories::name($p->cat)}</a></span>{/if}
                                        </div>
                                        <h2 class="post-title h3 mt-2"><a href="{Url::post($p->id)}" class="text-dark text-decoration-none fw-bold">{$p->title}</a></h2>
                                        <div class="entry-content mb-4 text-muted">
                                            {Typo::Xclean($p->content)|stripHtml|truncate:300}
                                        </div>
                                        <div class="post-footer d-flex justify-content-between align-items-center">
                                            <a href="{Url::post($p->id)}" class="btn btn-read-more">Read Article</a>
                                        </div>
                                    </div>
                                </article>
                            {/foreach}
                            <div class="pagination-wrapper mt-5 d-flex justify-content-center">
                                {$paging|noescape}
                            </div>
                        {else}
                            <div class="text-center py-5 bg-white shadow-sm">
                                <i class="fa fa-file-text-o fa-4x text-light mb-4"></i>
                                <h3 class="text-muted fw-bold">No Post to Show</h3>
                            </div>
                        {/if}
                    </div>
                {/if}
            </div>
            <div class="col-lg-4 col-md-12">
                {include 'rightside.php'}
            </div>
            </div>
        </div>
    </section>
{/if}
