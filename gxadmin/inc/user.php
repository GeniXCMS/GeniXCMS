<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 */
?>

<div class="col-md-12">
    <?=Hooks::run('admin_page_notif_action', $data);?>
</div>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6 text-start">
            <h3 class="fw-bold text-dark mb-0"><?=_("User Ecosystem");?></h3>
            <p class="text-muted small mb-0"><?=_("Oversee administrative access and member community.");?></p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#adduser">
                <i class="bi bi-person-plus-fill me-1"></i> <?=_("Onboard New User");?>
            </button>
        </div>
    </div>

    <!-- Stats Dashboard -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 d-flex flex-row align-items-center">
                <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary me-3">
                    <i class="bi bi-people fs-4"></i>
                </div>
                <div>
                    <div class="text-muted extra-small fw-bold text-uppercase"><?=_("Total Library");?></div>
                    <div class="h4 fw-bold m-0"><?=Stats::totalUser();?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 d-flex flex-row align-items-center">
                <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success me-3">
                    <i class="bi bi-person-check fs-4"></i>
                </div>
                <div>
                    <div class="text-muted extra-small fw-bold text-uppercase"><?=_("Active");?></div>
                    <div class="h4 fw-bold m-0"><?=Stats::activeUser();?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 d-flex flex-row align-items-center">
                <div class="bg-warning bg-opacity-10 p-3 rounded-3 text-warning me-3">
                    <i class="bi bi-clock-history fs-4"></i>
                </div>
                <div>
                    <div class="text-muted extra-small fw-bold text-uppercase"><?=_("Pending");?></div>
                    <div class="h4 fw-bold m-0"><?=Stats::pendingUser();?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 d-flex flex-row align-items-center">
                <div class="bg-danger bg-opacity-10 p-3 rounded-3 text-danger me-3">
                    <i class="bi bi-person-x fs-4"></i>
                </div>
                <div>
                    <div class="text-muted extra-small fw-bold text-uppercase"><?=_("Inactive");?></div>
                    <div class="h4 fw-bold m-0"><?=Stats::inactiveUser();?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 py-4 px-4">
            <form action="index.php?page=users" method="get" class="row g-3 align-items-end">
                <input type="hidden" name="page" value="users">
                <input type="hidden" name="token" value="<?=TOKEN;?>">
                
                <div class="col-lg-3 col-md-6">
                    <label class="form-label extra-small fw-bold text-uppercase text-muted"><?=_("Search Account");?></label>
                    <div class="input-group border rounded-pill overflow-hidden bg-light">
                        <span class="input-group-text bg-transparent border-0 ps-3"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control bg-transparent border-0 ps-2" placeholder="<?=_("Username or Email...");?>">
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <label class="form-label extra-small fw-bold text-uppercase text-muted"><?=_("Account Level");?></label>
                    <?php
                    $var = array('name' => 'group', 'class' => 'form-select border rounded-pill bg-light ps-3');
                    echo User::dropdown($var);
                    ?>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label extra-small fw-bold text-uppercase text-muted"><?=_("Account Status");?></label>
                    <select name="status" class="form-select border rounded-pill bg-light ps-3">
                        <option value="1"><?=_("Active Only");?></option>
                        <option value="0"><?=_("Inactive Only");?></option>
                    </select>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label extra-small fw-bold text-uppercase text-muted"><?=_("Join Date Range");?></label>
                    <div class="input-group">
                        <input type="date" name="from" class="form-control border rounded-pill-start bg-light ps-3" placeholder="<?=_("From");?>">
                        <input type="date" name="to" class="form-control border rounded-pill-end bg-light ps-3" placeholder="<?=_("To");?>">
                    </div>
                </div>

                <div class="col-lg-2 col-md-4">
                    <button type="submit" class="btn btn-dark rounded-pill w-100 px-3 py-2">
                        <i class="bi bi-funnel me-1"></i> <?=_("Apply Filters");?>
                    </button>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <form action="index.php?page=users" method="post">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted extra-small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3" style="width: 60px;"><?=_("ID");?></th>
                                <th><?=_("Identity");?></th>
                                <th><?=_("Permission Level");?></th>
                                <th class="text-center"><?=_("Journey Status");?></th>
                                <th class="text-center"><?=_("Origin");?></th>
                                <th class="text-end pe-4" style="width: 140px;"><?=_("Interaction");?></th>
                                <th class="text-center pe-4" style="width: 50px;">
                                    <input type="checkbox" id="selectall" class="form-check-input">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($data['num'] > 0):
                                foreach ($data['usr'] as $p):
                                    $grp = User::group($p->group);
                                    
                                    if ($p->status == '0') {
                                        $statusBadge = '<a href="index.php?page=users&act=active&id='.$p->id.'&token='.TOKEN.'" class="badge bg-danger bg-opacity-10 text-danger text-decoration-none">'._("Inactive").'</a>';
                                        $rowStyle = 'opacity: 0.8;';
                                    } else {
                                        $statusBadge = '<a href="index.php?page=users&act=inactive&id='.$p->id.'&token='.TOKEN.'" class="badge bg-success bg-opacity-10 text-success text-decoration-none">'._("Active").'</a>';
                                        $rowStyle = '';
                                    }
                                    $country = $p->country != "" ? strtolower($p->country) : "unknown";
                            ?>
                            <tr style="<?=$rowStyle;?>">
                                <td class="ps-4 text-muted small"><?=$p->id;?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 p-2 rounded-circle text-primary me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="bi bi-person fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark"><?=$p->userid;?></div>
                                            <div class="text-muted extra-small"><?=$p->email;?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-dark bg-opacity-10 text-dark border-0 px-3 fw-medium"><?=$grp;?></span>
                                </td>
                                <td class="text-center">
                                    <div class="mb-1"><?=$statusBadge;?></div>
                                    <div class="extra-small text-muted"><?=Date::format($p->join_date, 'd M Y');?></div>
                                </td>
                                <td class="text-center">
                                    <?php if($country != "unknown"): ?>
                                        <span class="flag-icon flag-icon-<?=$country;?> shadow-sm rounded-1" title="<?=strtoupper($country);?>"></span>
                                    <?php else: ?>
                                        <i class="bi bi-geo-alt text-muted opacity-50"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="index.php?page=users&act=edit&id=<?=$p->id;?>&token=<?=TOKEN;?>" class="btn btn-light btn-sm rounded-circle me-1 border" title="<?=_("Edit Profile");?>">
                                            <i class="bi bi-pencil-square text-success"></i>
                                        </a>
                                        <a href="index.php?page=users&act=del&id=<?=$p->id;?>&token=<?=TOKEN;?>" 
                                           class="btn btn-light btn-sm rounded-circle border" 
                                           onclick="return confirm('<?=_("Are you sure you want to remove this user permanently?");?>');" title="<?=_("Delete Account");?>">
                                            <i class="bi bi-trash text-danger"></i>
                                        </a>
                                    </div>
                                </td>
                                <td class="text-center pe-4">
                                    <input type="checkbox" name="user_id[]" value="<?=$p->id;?>" class="check form-check-input">
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="opacity-25 mb-3"><i class="bi bi-people-fill fs-1"></i></div>
                                    <p class="text-muted mb-0"><?=_("No users found matching your search criteria.");?></p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Card Footer with Batch Actions and Pagination -->
                <div class="card-footer bg-white border-top py-4 px-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <select name="action" class="form-select form-select-sm rounded-pill bg-light" style="width: 160px;">
                            <option value="activate"><?=_("Batch Activate");?></option>
                            <option value="deactivate"><?=_("Batch Deactivate");?></option>
                            <option value="delete"><?=_("Batch Delete");?></option>
                        </select>
                        <button type="submit" name="doaction" class="btn btn-danger btn-sm rounded-pill px-4">
                            <i class="bi bi-lightning-fill me-1"></i> <?=_("Start Action");?>
                        </button>
                        <input type="hidden" name="token" value="<?=TOKEN;?>">
                    </div>
                    <div>
                        <?=$data['paging'];?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="adduser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="index.php?page=users" method="post">
                <div class="modal-header border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark m-0"><?=_("Onboard New User");?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Username");?></label>
                        <input type="text" name="userid" class="form-control border-0 bg-light rounded-3 py-2 px-3" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Password");?></label>
                            <input type="password" name="pass1" class="form-control border-0 bg-light rounded-3 py-2 px-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Verify");?></label>
                            <input type="password" name="pass2" class="form-control border-0 bg-light rounded-3 py-2 px-3" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Email Address");?></label>
                        <input type="email" name="email" class="form-control border-0 bg-light rounded-3 py-2 px-3" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Permission Group");?></label>
                        <?php
                        $var = array('name' => 'group', 'selected' => '6', 'update' => true, 'class' => 'form-select border-0 bg-light rounded-3 py-2 px-3');
                        echo User::dropdown($var);
                        ?>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal"><?=_("Cancel");?></button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm" name="adduser"><?=_("Create Account");?></button>
                </div>
                <input type="hidden" name="token" value="<?=TOKEN;?>">
            </form>
        </div>
    </div>
</div>

<style>
    .rounded-pill-start { border-radius: 50rem 0 0 50rem !important; }
    .rounded-pill-end { border-radius: 0 50rem 50rem 0 !important; }
    .flag-icon { width: 20px; height: 15px; display: inline-block; background-size: contain; background-position: center; background-repeat: no-repeat; }
</style>
