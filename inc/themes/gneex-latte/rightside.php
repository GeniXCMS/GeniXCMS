{if Gneex::opt('adsense')}
    <div class="row mb-4"><div class="col-md-12 text-center">{Gneex::opt('adsense')|noescape}</div></div>
{/if}

<div class="sidebar-cards">
    {* Handle Dynamic Widgets if any *}
    {if class_exists('Widget')}
        {Widget::show('sidebar')|noescape}
    {/if}

    {* Fallback to default if no dynamic widgets active in sidebar location *}
    {if !Db::result("SELECT * FROM `widgets` WHERE `status` = '1' AND `location` = 'sidebar'")}
        <div class="card shadow-sm border-0 mb-4" data-aos="fade-left">
            <div class="card-header"><h3 class="card-title m-0">Recent Posts</h3></div>
            <div class="card-body">
                {Posts::lists([
                    'num' => 5,
                    'title' => true,
                    'image' => true,
                    'image_size' => 60,
                    'class' => ['row' => 'mb-3', 'img' => 'rounded shadow-sm', 'h4' => 'fs-6 fw-bold m-0']
                ])|noescape}
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4" data-aos="fade-left">
            <div class="card-header"><h3 class="card-title m-0">Recent Comments</h3></div>
            <div class="card-body">{Comments::recent()|noescape}</div>
        </div>

        <div class="card shadow-sm border-0 mb-4" data-aos="fade-left">
            <div class="card-header"><h3 class="card-title m-0">Popular Tags</h3></div>
            <div class="card-body">
                <div class="tag-cloud-wrapper">{Tags::cloud()|noescape}</div>
            </div>
        </div>
    {/if}
</div>
