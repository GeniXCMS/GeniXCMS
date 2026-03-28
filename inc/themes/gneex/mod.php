    <!-- DEBUG: MOD.LATTE START -->
{var $opt = Gneex::$opt}
<section id="blog" class="py-5 bg-light min-vh-100">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8 col-md-12">
                <div class="blog-lists">
                    {if Gneex::opt('adsense')}
                        <div class="text-center mb-4">{Gneex::opt('adsense')|noescape}</div>
                    {/if}

                    <article class="blog-post p-4 p-lg-5 overflow-hidden shadow-sm bg-white border-0 mb-4 rounded-4">
                        <div class="entry-content">
                            {Hooks::run('mod_control', $data)|noescape}
                        </div>
                    </article>

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
