<section class="error-page py-5 bg-light min-vh-100 d-flex align-items-center">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="error-card bg-white p-5 shadow-sm rounded-4 border-0" data-aos="zoom-in">
                    <div class="error-visual mb-4">
                        <h1 class="display-1 fw-900 mb-0" style="font-size: 15rem; color: #f1f5f9; letter-spacing: -10px; line-height: 1;">404</h1>
                        <div class="error-icon position-relative" style="margin-top: -80px;">
                            <i class="fa-solid fa-map-location-dot fa-5x text-primary shadow-sm rounded-circle p-4 bg-white"></i>
                        </div>
                    </div>
                    
                    <h2 class="fw-bold mb-3 mt-4 h1">Page Not Found</h2>
                    <p class="text-muted mb-5 px-lg-5 lead">
                        We apologize, but the page you are looking for seems to have moved or no longer exists. 
                    </p>

                    <!-- Search Form Refined -->
                    <div class="mx-auto mb-5" style="max-width: 500px;">
                        <form action="{Url::search()}" method="GET" class="input-group search-wrap-404 shadow-sm">
                            <input type="text" name="q" class="form-control border-0 ps-4 py-3 bg-white" placeholder="Search our site..." required style="border-radius: 50px 0 0 50px !important;">
                            <button type="submit" class="btn btn-dark px-4 py-3" style="border-radius: 0 50px 50px 0 !important; min-width: 100px;">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        </form>
                    </div>

                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="{Site::$url}" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-sm">
                            <i class="fa-solid fa-house me-2"></i> Go to Homepage
                        </a>
                        <a href="javascript:history.back()" class="btn btn-outline-secondary rounded-pill px-5 py-3 fw-bold">
                            <i class="fa-solid fa-arrow-left me-2"></i> Previous Page
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.search-wrap-404 {
    border-radius: 50px;
    background: #fff;
    border: 1px solid #e2e8f0;
    overflow: hidden;
}
.search-wrap-404 .form-control:focus {
    box-shadow: none;
    background: #fff;
}
.search-wrap-404 .btn-dark {
    background: #1e293b; /* Industrial Dark Blue/Grey */
    border: none;
    font-weight: 700;
}
.search-wrap-404 .btn-dark:hover {
    background: #0f172a;
}
.error-page .fw-900 { font-weight: 900; }
.error-visual h1 {
    user-select: none;
    background: linear-gradient(to bottom, #f1f5f9, #cbd5e1);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
</style>
