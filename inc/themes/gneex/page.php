{var $opt = Gneex::$opt}
<section id="blog" class="py-5 bg-light min-vh-100">
    <div class="container">
        <div class="row g-4">
            {var $bar = Posts::getParam('sidebar', $posts[0]->id)}
            {var $cols = ($bar == 'yes' || $bar == '') ? '8': '12'}
            
            <div class="col-lg-{$cols} col-md-12">
                <div class="blog-lists">
                    {if Gneex::opt('adsense')}
                        <div class="text-center mb-4">{Gneex::opt('adsense')|noescape}</div>
                    {/if}

                    <article class="blog-post p-0 overflow-hidden shadow-sm bg-white border-0 mb-5">
                        {if isset($imgurl)}
                            <div class="post-header-img-single">
                                <img src="{$imgurl}" class="img-fluid w-100" style="max-height: 520px; object-fit: cover;">
                            </div>
                        {/if}
                        
                        <div class="post-inner-card">
                            <div class="entry-content mb-5">
                                {$content|noescape}
                            </div>
                            
                            {* Social Share — below content *}
                            <div class="share-bar mt-4 pt-4 border-top">
                                <span class="text-muted small fw-bold text-uppercase me-3">Share this page:</span>
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
                    </article>

                    {if Gneex::opt('adsense')}
                        <div class="text-center mb-4">{Gneex::opt('adsense')|noescape}</div>
                    {/if}
                </div>
            </div>
            {if $bar == 'yes' || $bar == ''}
                <div class="col-lg-4 col-md-12">
                    {include 'rightside.php'}
                </div>
            {/if}
        </div>
    </div>
</section>
