<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 */
$username = Session::val('username');
?>

<div class="col-md-12">
    <?=Hooks::run('admin_page_notif_action', $data);?>
    <?=Hooks::run('admin_page_top_action', $data);?>
</div>

<div class="container-fluid py-4">
    <!-- Premium Header Section -->
    <div class="row align-items-center mb-5 mt-2">
        <div class="col-lg-8 text-start">
            <h2 class="fw-black text-dark mb-1 d-flex align-items-center gap-2">
                <span><?=_("Welcome back");?>,</span>
                <span class="text-primary text-gradient"><?=htmlspecialchars($username);?>!</span>
                <span class="wave-emoji fs-3">👋</span>
            </h2>
            <p class="text-muted fw-medium fs-6 mb-0">
                <i class="bi bi-calendar3 me-2 text-primary opacity-50"></i>
                <?=Date::local(date('Y-m-d H:i:s'), 'EEEE, d MMMM yyyy');?> · <span class="text-dark fw-bold" id="dashboard-clock"><?=date('H:i');?></span>
            </p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <div class="dropdown d-inline-block">
                <button class="btn btn-white shadow-sm rounded-pill px-4 border py-2 fw-bold d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                    <i class="bi bi-speedometer2 text-primary"></i> <?=_("System Status");?>
                    <span class="pulse-success ms-1"></span>
                </button>
                <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-3 rounded-4" style="min-width: 280px;">
                    <h6 class="fw-bold extra-small text-uppercase tracking-wider text-muted mb-3"><?=_("Server Vitals");?></h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small text-muted"><?=_("PHP Version");?></span>
                        <span class="badge bg-light text-dark fw-bold"><?=PHP_VERSION;?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small text-muted"><?=_("OS");?></span>
                        <span class="badge bg-light text-dark fw-bold"><?=PHP_OS;?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small text-muted"><?=_("Core Engine");?></span>
                        <span class="badge bg-primary bg-opacity-10 text-primary fw-bold">v<?=System::$version;?></span>
                    </div>
                    <hr class="opacity-10">
                    <a href="index.php?page=settings" class="btn btn-primary btn-sm w-100 rounded-pill"><?=_("Manage Config");?></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Stats Cards -->
    <div class="row g-4 mb-5">
        <!-- Posts Stats -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-5 h-100 p-2 overflow-hidden position-relative stats-card stats-primary">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="stats-icon-bg bg-primary bg-opacity-10 rounded-4 d-flex align-items-center justify-content-center">
                            <i class="bi bi-journal-text fs-3 text-primary"></i>
                        </div>
                        <div class="text-end">
                            <div class="extra-small fw-bold text-muted text-uppercase tracking-widest mb-1"><?=_("Content Library");?></div>
                            <h2 class="fw-black m-0 mb-n1 counter-value"><?=Stats::totalPost('post');?></h2>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="extra-small text-muted fw-bold">
                            <span class="text-success"><i class="bi bi-plus-circle me-1"></i><?=_("Articles");?></span>
                        </div>
                        <a href="index.php?page=posts" class="btn btn-light btn-sm rounded-pill p-1 ps-3 pe-3 fs-8 fw-bold">
                            <?=_("View All");?> <i class="bi bi-chevron-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="stats-decoration"></div>
            </div>
        </div>

        <!-- Pages Stats -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-5 h-100 p-2 overflow-hidden position-relative stats-card stats-success">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="stats-icon-bg bg-success bg-opacity-10 rounded-4 d-flex align-items-center justify-content-center">
                            <i class="bi bi-stack fs-3 text-success"></i>
                        </div>
                        <div class="text-end">
                            <div class="extra-small fw-bold text-muted text-uppercase tracking-widest mb-1"><?=_("Structure");?></div>
                            <h2 class="fw-black m-0 mb-n1 counter-value"><?=Stats::totalPost('page');?></h2>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="extra-small text-muted fw-bold">
                            <span class="text-primary"><i class="bi bi-layers me-1"></i><?=_("Active Pages");?></span>
                        </div>
                        <a href="index.php?page=pages" class="btn btn-light btn-sm rounded-pill p-1 ps-3 pe-3 fs-8 fw-bold">
                            <?=_("Manage");?> <i class="bi bi-chevron-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="stats-decoration"></div>
            </div>
        </div>

        <!-- Comments Stats -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-5 h-100 p-2 overflow-hidden position-relative stats-card stats-warning">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="stats-icon-bg bg-warning bg-opacity-10 rounded-4 d-flex align-items-center justify-content-center">
                            <i class="bi bi-chat-left-dots fs-3 text-warning"></i>
                        </div>
                        <div class="text-end">
                            <div class="extra-small fw-bold text-muted text-uppercase tracking-widest mb-1"><?=_("Engagement");?></div>
                            <h2 class="fw-black m-0 mb-n1 counter-value"><?=Stats::pendingComments();?></h2>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="extra-small text-muted fw-bold">
                            <span class="text-danger"><i class="bi bi-exclamation-circle me-1"></i><?=_("Pending");?></span>
                        </div>
                        <a href="index.php?page=comments" class="btn btn-light btn-sm rounded-pill p-1 ps-3 pe-3 fs-8 fw-bold">
                            <?=_("Verify");?> <i class="bi bi-chevron-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="stats-decoration"></div>
            </div>
        </div>

        <!-- Users Stats -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-5 h-100 p-2 overflow-hidden position-relative stats-card stats-info">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="stats-icon-bg bg-info bg-opacity-10 rounded-4 d-flex align-items-center justify-content-center">
                            <i class="bi bi-people fs-3 text-info"></i>
                        </div>
                        <div class="text-end">
                            <div class="extra-small fw-bold text-muted text-uppercase tracking-widest mb-1"><?=_("Total Base");?></div>
                            <h2 class="fw-black m-0 mb-n1 counter-value"><?=Stats::totalUser();?></h2>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="extra-small text-muted fw-bold">
                            <span class="text-dark"><i class="bi bi-shield-check me-1"></i><?=_("Verified Users");?></span>
                        </div>
                        <a href="index.php?page=users" class="btn btn-light btn-sm rounded-pill p-1 ps-3 pe-3 fs-8 fw-bold">
                            <?=_("Analyze");?> <i class="bi bi-chevron-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="stats-decoration"></div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <!-- Recent Activity Feed -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-5 overflow-hidden h-100">
                <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-dark m-0"><?=_("Content Velocity");?></h5>
                        <p class="extra-small text-muted mb-0"><?=_("Automated log of recently published architecture.");?></p>
                    </div>
                    <a href="index.php?page=posts&act=add" class="btn btn-primary rounded-circle p-2 shadow-sm">
                        <i class="bi bi-plus-lg"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light bg-opacity-50 text-muted extra-small text-uppercase tracking-widest fw-bold">
                                <tr>
                                    <th class="ps-4 py-3"><?=_("Object Identity");?></th>
                                    <th><?=_("Author");?></th>
                                    <th><?=_("Status");?></th>
                                    <th class="text-end pe-4"><?=_("Aesthetics");?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $vars = array('num' => 6, 'type' => 'post');
                                $post = Posts::recent($vars);
                                if (!isset($post['error'])):
                                    foreach ($post as $p):
                                ?>
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="fw-bold text-dark fs-7 mb-0"><?=$p->title;?></div>
                                        <div class="extra-small text-muted opacity-75"><?=Date::format($p->date);?></div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-light rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                                <i class="bi bi-person extra-small"></i>
                                            </div>
                                            <span class="small fw-semibold text-muted"><?=$p->author;?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 extra-small border border-success border-opacity-10"><?=_("Live");?></span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group shadow-none">
                                            <a href="<?=Url::post($p->id);?>" target="_blank" class="btn btn-white btn-sm rounded-circle border p-1" title="<?=_("View");?>">
                                                <i class="bi bi-eye text-primary"></i>
                                            </a>
                                            <a href="index.php?page=posts&act=edit&id=<?=$p->id;?>" class="btn btn-white btn-sm rounded-circle border p-1 ms-1" title="<?=_("Edit");?>">
                                                <i class="bi bi-pencil-square text-dark"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="opacity-25 mb-2"><i class="bi bi-cloud-slash fs-1"></i></div>
                                        <p class="text-muted extra-small fw-bold mb-0"><?=_("Static Database: No recent signals detected.");?></p>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 py-3 text-center border-top">
                    <a href="index.php?page=posts" class="text-primary text-decoration-none extra-small fw-black text-uppercase tracking-wider">
                        <?=_("Access Entire Content Repository");?> <i class="bi bi-arrow-right-short ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Latest Members -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-5 h-100 overflow-hidden">
                <div class="card-header bg-white border-0 py-4 px-4">
                    <h5 class="fw-bold text-dark m-0"><?=_("New Entities");?></h5>
                    <p class="extra-small text-muted mb-0"><?=_("Recently registered collaborators.");?></p>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="row g-4 text-center">
                        <?php
                        $users = Db::result("SELECT * FROM `user` ORDER BY `join_date` DESC LIMIT 6");
                        if (Db::$num_rows > 0) {
                            foreach ($users as $u) {
                                $avatar = Image::getGravatar($u->email);
                                ?>
                                <div class="col-4">
                                    <div class="p-2 member-card rounded-4 transition-all">
                                        <div class="position-relative d-inline-block mb-2">
                                            <img src="<?=$avatar;?>" class="rounded-circle shadow-sm border border-3 border-white" width="55" alt="Avatar">
                                            <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle p-1" style="width: 12px; height: 12px;"></span>
                                        </div>
                                        <div class="fw-bold fs-8 text-dark text-truncate mb-0 px-1"><?=htmlspecialchars($u->userid);?></div>
                                        <div class="extra-small text-muted opacity-50"><?=Date::format($u->join_date, 'M d');?></div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="card-footer bg-light bg-opacity-50 border-0 py-3 text-center border-top">
                    <a href="index.php?page=users" class="btn btn-white btn-sm rounded-pill fw-bold border px-4 shadow-sm w-100">
                        <?=_("User Directory Management");?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Geo Insights -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-5 h-100 overflow-hidden">
                <div class="card-header bg-white border-0 py-4 px-4 sticky-top">
                    <h6 class="fw-bold text-dark m-0 fs-5"><i class="bi bi-globe-americas me-2 text-primary"></i><?=_("Geographical Footprint");?></h6>
                    <p class="extra-small text-muted mb-0"><?=_("Origin signals of your audience base.");?></p>
                </div>
                <div class="card-body p-0">
                    <div id="world-map" class="bg-light" style="height: 350px;"></div>
                </div>
            </div>
        </div>

        <!-- Viral Performance -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-5 h-100 overflow-hidden">
                <div class="card-header bg-white border-0 py-4 px-4">
                    <h6 class="fw-bold text-dark m-0 fs-5"><i class="bi bi-lightning-charge-fill me-2 text-warning"></i><?=_("Peak Performance");?></h6>
                    <p class="extra-small text-muted mb-0"><?=_("Content units with the highest engagement signal.");?></p>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <tbody>
                                <?php
                                $list = Stats::mostViewed(6);
                                if (!isset($list['error'])):
                                    foreach ($list as $p):
                                ?>
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="bi bi-graph-up-arrow fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark fs-7 mb-0"><?=$p->title;?></div>
                                                <div class="extra-small text-muted"><i class="bi bi-eye me-1"></i> <?=number_format($p->views);?> <?=_("Views accumulated");?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="<?=Url::post($p->id);?>" target="_blank" class="btn btn-light btn-sm rounded-pill p-2 fs-8 fw-bold">
                                            <i class="bi bi-arrow-up-right"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; else: ?>
                                    <tr><td class="text-center py-5 opacity-25 fw-bold text-muted"><?=_("Signal data missing.");?></td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom Design Tokens */
    .fw-black { font-weight: 900 !important; }
    .text-gradient { background: linear-gradient(45deg, #0d6efd, #0dcaf0); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .wave-emoji { display: inline-block; animation: wave 2.5s infinite; transform-origin: 70% 70%; }
    
    .stats-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: default; }
    .stats-card:hover { transform: translateY(-8px); }
    .stats-icon-bg { width: 60px; height: 60px; transition: all 0.3s; }
    .stats-card:hover .stats-icon-bg { transform: scale(1.1) rotate(-8deg); }
    
    .stats-decoration { position: absolute; bottom: -20px; right: -20px; width: 100px; height: 100px; background: currentColor; opacity: 0.03; border-radius: 50%; z-index: 0; pointer-events: none; }
    .stats-primary { color: #0d6efd; }
    .stats-success { color: #198754; }
    .stats-warning { color: #ffc107; }
    .stats-info { color: #0dcaf0; }

    .member-card:hover { background-color: #f8f9fa; transform: translateY(-4px); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .pulse-success { display: inline-block; width: 8px; height: 8px; background: #198754; border-radius: 50%; animation: pulse 1.5s infinite; }

    @keyframes wave {
        0% { transform: rotate( 0.0deg) }
        10% { transform: rotate(14.0deg) }
        20% { transform: rotate(-8.0deg) }
        30% { transform: rotate(14.0deg) }
        40% { transform: rotate(-4.0deg) }
        50% { transform: rotate(10.0deg) }
        60% { transform: rotate( 0.0deg) }
        100% { transform: rotate( 0.0deg) }
    }

    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(25, 135, 84, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); }
    }

    .fs-7 { font-size: 0.9rem !important; }
    .fs-8 { font-size: 0.8rem !important; }
    .extra-small { font-size: 0.7rem !important; }
    .tracking-widest { letter-spacing: 0.1em; }
</style>

<script>
    // Real-time Clock Logic
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        document.getElementById('dashboard-clock').textContent = hours + ':' + minutes;
    }
    setInterval(updateClock, 30000);
</script>
