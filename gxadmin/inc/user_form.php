<?php
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * 
 * @since 0.0.1
 * @version 2.1.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

$id = isset($_GET['id']) ? Typo::int($_GET['id']) : '';
?>
<form action="" method="post">
    <div class="col-md-12">
        <?= Hooks::run('admin_page_notif_action', $data); ?>
    </div>

    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="row align-items-center mb-4">
            <div class="col-md-6 text-start">
                <h3 class="fw-bold text-dark mb-0"><?= _("Profile Configuration"); ?></h3>
                <p class="text-muted small mb-0">
                    <?= _("Refine account details and permission levels for this user."); ?>
                </p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <div class="btn-group gap-2">
                    <button class="btn btn-primary rounded-pill px-4 shadow-sm" type="submit" name="edituser">
                        <i class="bi bi-check2-circle me-1"></i> <?= _("Save Evolution"); ?>
                    </button>
                    <a class="btn btn-light border rounded-pill px-4"
                        href="<?= (User::access(2)) ? 'index.php?page=users' : 'index.php'; ?>">
                        <i class="bi bi-arrow-left me-1"></i> <?= _("Return back"); ?>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Identity Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-1">
                        <h6 class="fw-bold text-muted extra-small text-uppercase tracking-wider m-0">
                            <?= _("Core Identity"); ?>
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label
                                    class="form-label small fw-bold text-muted text-uppercase"><?= _("Username"); ?></label>
                                <?php if (User::access(0)) {
                                    $userid = User::userid($id);
                                    ?>
                                    <input type="text" name="userid"
                                        class="form-control border-0 bg-light rounded-3 py-2 px-3" value="<?= $userid; ?>">
                                    <input type="hidden" name="olduserid" class="form-control" value="<?= $userid; ?>">
                                    <div class="extra-small text-muted mt-1 ps-1">
                                        <?= _("Only administrative access can modify the unique ID."); ?>
                                    </div>
                                <?php } else { ?>
                                    <div class="form-control border-0 bg-light rounded-3 py-2 px-3 text-muted">
                                        <?= $userid; ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="col-md-6">
                                <label
                                    class="form-label small fw-bold text-muted text-uppercase"><?= _("Email Contact"); ?></label>
                                <input type="text" name="email"
                                    class="form-control border-0 bg-light rounded-3 py-2 px-3"
                                    value="<?= User::email($id); ?>">
                                <div class="extra-small text-muted mt-1 ps-1">
                                    <?= _("Used for communications and recovery."); ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label
                                    class="form-label small fw-bold text-muted text-uppercase"><?= _("Security Shield (Password)"); ?></label>
                                <input type="password" name="pass"
                                    class="form-control border-0 bg-light rounded-3 py-2 px-3" placeholder="••••••••">
                                <div class="extra-small text-muted mt-1 ps-1">
                                    <?= _("Leave empty to preserve existing authentication credentials."); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Access Control Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-1">
                        <h6 class="fw-bold text-muted extra-small text-uppercase tracking-wider m-0">
                            <?= _("Access Protocol"); ?>
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <?php if (User::access(1)) { ?>
                            <div class="mb-3">
                                <label
                                    class="form-label small fw-bold text-muted text-uppercase"><?= _("Permission Rank"); ?></label>
                                <?php
                                $var = array(
                                    'name' => 'group',
                                    'selected' => User::group($id),
                                    'update' => true,
                                    'class' => 'form-select border-0 bg-light rounded-3 py-2 px-3'
                                );
                                echo User::dropdown($var); ?>
                                <div class="extra-small text-muted mt-2">
                                    <i class="bi bi-shield-lock me-1"></i> <?= _("Defines administrative capabilities."); ?>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="text-center py-4 bg-light rounded-4 opacity-75">
                                <i class="bi bi-lock fs-2 text-muted"></i>
                                <p class="extra-small fw-bold text-muted text-uppercase m-0"><?= _("Restricted Access"); ?>
                                </p>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- Info Card -->
                <div
                    class="card border-0 shadow-sm rounded-4 bg-primary bg-opacity-10 border-start border-4 border-primary">
                    <div class="card-body p-4">
                        <div class="d-flex gap-3 align-items-center mb-3">
                            <i class="bi bi-info-circle-fill text-primary fs-4"></i>
                            <h6 class="fw-bold text-dark m-0"><?= _("Pro Tip"); ?></h6>
                        </div>
                        <p class="extra-small text-muted mb-0">
                            <?= _("Updating user credentials might require the user to re-authenticate their session. Changes take effect immediately upon saving."); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="token" value="<?= TOKEN ?>">
</form>