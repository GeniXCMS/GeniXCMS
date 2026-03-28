<section id="blog" class="py-5 min-vh-100">
    <div class="container">
        <div class="row g-4">
            {var $bar = Posts::getParam('sidebar', $posts[0]->id)}

            {* Article *}
            <div class="{($bar == 'yes'|| $bar == '') ? 'col-lg-8': 'col-lg-12'} col-md-12">
                <div class="blog-lists">
                    {if Gneex::opt('adsense')}
                        <div class="text-center mb-4">{Gneex::opt('adsense')|noescape}</div>
                    {/if}

                    <article class="blog-post p-0 overflow-hidden mb-5">
                        {var $p_img = Gneex::getImage($content, $posts[0]->id)}
                        {if $p_img}
                            <div class="post-header-img-single">
                                <img src="{$p_img}" class="img-fluid w-100" style="max-height: 520px; object-fit: cover;">
                            </div>
                        {/if}

                        <div class="post-inner-card">
                            <div class="post-meta-details">
                                <span><i class="fa-solid fa-folder-open me-2"></i> <a href="{Url::cat($posts[0]->cat)}" class="text-decoration-none"> {Categories::name($posts[0]->cat)}</a></span>
                                <span><i class="fa-regular fa-calendar me-2"></i> {$date_published}</span>
                                <span><i class="fa-regular fa-circle-user me-2"></i> <a href="{$url_author}" class="text-decoration-none"> {$author}</a></span>
                            </div>

                            <div class="entry-content mb-5">
                                {$content|noescape}
                            </div>

                            <div class="tags-section mt-4 pt-4 border-top">
                                {Posts::tags($posts[0]->id, '<span class="text-muted small fw-bold text-uppercase me-2">Tags</span>')|noescape}
                            </div>

                            {* Social Share — below article *}
                            <div class="share-bar mt-4 pt-4 border-top">
                                <span class="text-muted small fw-bold text-uppercase me-3">Share:</span>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={Site::canonical()}" target="_blank" class="share-btn share-fb" title="Share on Facebook">
                                    <i class="fa-brands fa-facebook-f"></i> Facebook
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={Site::canonical()}&text={$title}" target="_blank" class="share-btn share-tw" title="Share on X / Twitter">
                                    <i class="fa-brands fa-x-twitter"></i> Twitter
                                </a>
                                <a href="https://wa.me/?text={$title}%20{Site::canonical()}" target="_blank" class="share-btn share-wa" title="Share on WhatsApp">
                                    <i class="fa-brands fa-whatsapp"></i> WhatsApp
                                </a>
                            </div>
                        </div>

                        <div class="related-posts p-4 p-lg-5 border-top">
                            <h4 class="fw-bold mb-4">You Might Also Like</h4>
                            {Posts::related($posts[0]->id, 3, $posts[0]->cat, 'box')|noescape}
                        </div>
                    </article>

                    {if Comments::isEnable()}
                        <div class="comments-container mb-5">
                            <h3 class="fw-bold mb-4">Post Comments</h3>
                            <div class="card border-0 shadow-sm p-4 p-md-5 mb-5">
                                {Comments::form()|noescape}
                            </div>
                            <div class="comment-list-wrapper mt-5">
                                {Comments::showList(['offset' => 0, 'max' => Options::v('comments_perpage'), 'parent' => 0])|noescape}
                            </div>
                        </div>
                    {/if}
                </div>
            </div>

            {if $bar == 'yes'|| $bar == ''}
                <div class="col-lg-4 col-md-12">
                    {include 'rightside.php'}
                </div>
            {/if}
        </div>
    </div>
</section>
