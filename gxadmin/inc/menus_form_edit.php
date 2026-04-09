<?php
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * 
 * @since 0.0.1
 * @version 2.2.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

if (isset($_GET['id'])) {
    $menuid = Typo::cleanX($_GET['id']);
} else {
    $menuid = $data['menus'][0]->menuid;
}

if (isset($_GET['token']) && Token::validate(Typo::cleanX($_GET['token']))) {
    $token = TOKEN;
} else {
    $token = '';
}
?>
<form action="" method="POST">
    <div class="col-md-12">
        <?= Hooks::run('admin_page_notif_action', $data); ?>
    </div>

    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="row align-items-center mb-4">
            <div class="col-md-6 text-start">
                <h3 class="fw-bold text-dark mb-0"><?= _("Link Adjustment"); ?></h3>
                <p class="text-muted small mb-0">
                    <?= _("Modify the target, label, and hierarchical position of this menu entry."); ?>
                </p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <div class="btn-group gap-2">
                    <button type="submit" name="edititem" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> <?= _("Apply Changes"); ?>
                    </button>
                    <a href="index.php?page=menus" class="btn btn-light border rounded-pill px-4">
                        <i class="bi bi-arrow-left me-1"></i> <?= _("Back to Menu"); ?>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-1">
                        <h6 class="fw-bold text-muted extra-small text-uppercase tracking-wider m-0">
                            <?= _("Configuration"); ?>
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Parent & Info -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label
                                        class="form-label small fw-bold text-muted text-uppercase"><?= _("Hierarchical Parent"); ?></label>
                                    <select class="form-select border-0 bg-light rounded-3 py-2 px-3" name="parent">
                                        <option value="0"><?= _("Top Level (None)"); ?></option>
                                        <?php
                                        foreach ($data['parent'] as $p) {
                                            if ($p->parent == '0') {
                                                $sel = ($data['menus'][0]->parent == $p->id) ? 'SELECTED' : '';
                                                echo "<option value=\"$p->id\" $sel>$p->name</option>";
                                                foreach ($data['parent'] as $p2) {
                                                    if ($p2->parent == $p->id) {
                                                        $sel = ($data['menus'][0]->parent == $p2->id) ? 'SELECTED' : '';
                                                        echo "<option value=\"$p2->id\" $sel>&nbsp;&nbsp;&nbsp;$p2->name</option>";
                                                        foreach ($data['parent'] as $p3) {
                                                            if ($p3->parent == $p2->id) {
                                                                $sel = ($data['menus'][0]->parent == $p3->id) ? 'SELECTED' : '';
                                                                echo "<option value=\"$p3->id\" $sel>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$p3->name</option>";
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label
                                        class="form-label small fw-bold text-muted text-uppercase"><?= _("Registry ID"); ?></label>
                                    <input type="text" name='id'
                                        class="form-control border-0 bg-white rounded-3 py-2 px-3 shadow-none"
                                        value="<?= $menuid; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label
                                        class="form-label small fw-bold text-muted text-uppercase"><?= _("CSS Class"); ?></label>
                                    <input type="text" name='class'
                                        class="form-control border-0 bg-light rounded-3 py-2 px-3"
                                        value="<?= $data['menus'][0]->class; ?>">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-4">
                                    <label
                                        class="form-label small fw-bold text-muted text-uppercase"><?= _("Label Text"); ?></label>
                                    <input type="text" name='name'
                                        class="form-control border-0 bg-light rounded-4 py-3 px-4 fw-bold fs-5 text-primary"
                                        value="<?= $data['menus'][0]->name; ?>" required>
                                    <div class="extra-small text-muted mt-1 ps-2">
                                        <?= _("Visual text displayed on the navigation bar."); ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Menu Type Selection -->
                            <div class="col-12 mt-2">
                                <h6
                                    class="fw-bold text-dark extra-small text-uppercase tracking-widest mb-3 border-bottom pb-2">
                                    <?= _("Redirect Target Type"); ?>
                                </h6>
                                <div class="row g-3">
                                    <!-- Internal Page -->
                                    <div class="col-md-6">
                                        <div
                                            class="card border-0 bg-light rounded-4 p-3 h-100 <?= ($data['menus'][0]->type == 'page') ? 'border-start border-4 border-primary shadow-sm' : '' ?>">
                                            <div class="form-check d-flex align-items-start gap-2">
                                                <input type="radio" name='type' id="typePage"
                                                    class="form-check-input mt-1" value="page"
                                                    <?= ($data['menus'][0]->type == 'page') ? 'checked' : '' ?>>
                                                <label class="form-check-label flex-grow-1" for="typePage">
                                                    <div class="fw-bold text-dark small"><?= _("Internal Page"); ?>
                                                    </div>
                                                    <div class="extra-small text-muted mb-2">
                                                        <?= _("Connect to a static page."); ?>
                                                    </div>
                                                    <?php
                                                    $vars = array(
                                                        'name' => 'page',
                                                        'type' => 'page',
                                                        'sort' => 'ASC',
                                                        'selected' => $data['menus'][0]->value,
                                                        'order_by' => 'title',
                                                        'class' => 'form-select form-select-sm border-0 bg-white rounded-pill px-3 mt-1'
                                                    );
                                                    echo Posts::dropdown($vars);
                                                    ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Category Archive -->
                                    <div class="col-md-6">
                                        <div
                                            class="card border-0 bg-light rounded-4 p-3 h-100 <?= ($data['menus'][0]->type == 'cat') ? 'border-start border-4 border-primary shadow-sm' : '' ?>">
                                            <div class="form-check d-flex align-items-start gap-2">
                                                <input type="radio" name='type' id="typeCat"
                                                    class="form-check-input mt-1" value="cat"
                                                    <?= ($data['menus'][0]->type == 'cat') ? 'checked' : '' ?>>
                                                <label class="form-check-label flex-grow-1" for="typeCat">
                                                    <div class="fw-bold text-dark small"><?= _("Category View"); ?>
                                                    </div>
                                                    <div class="extra-small text-muted mb-2">
                                                        <?= _("Link to category archives."); ?>
                                                    </div>
                                                    <?php
                                                    $vars = array(
                                                        'name' => 'cat',
                                                        'sort' => 'ASC',
                                                        'selected' => $data['menus'][0]->value,
                                                        'order_by' => 'name',
                                                        'class' => 'form-select form-select-sm border-0 bg-white rounded-pill px-3 mt-1'
                                                    );
                                                    echo Categories::dropdown($vars);
                                                    ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- System Module -->
                                    <div class="col-md-6">
                                        <div
                                            class="card border-0 bg-light rounded-4 p-3 h-100 <?= ($data['menus'][0]->type == 'mod') ? 'border-start border-4 border-primary shadow-sm' : '' ?>">
                                            <div class="form-check d-flex align-items-start gap-2">
                                                <input type="radio" name='type' id="typeMod"
                                                    class="form-check-input mt-1" value="mod"
                                                    <?= ($data['menus'][0]->type == 'mod') ? 'checked' : '' ?>>
                                                <label class="form-check-label flex-grow-1" for="typeMod">
                                                    <div class="fw-bold text-dark small"><?= _("System Module"); ?>
                                                    </div>
                                                    <div class="extra-small text-muted mb-2">
                                                        <?= _("Direct access to modular features."); ?>
                                                    </div>
                                                    <?php
                                                    $val = ($data['menus'][0]->type == 'mod') ? $data['menus'][0]->value : '';
                                                    ?>
                                                    <select name="mod"
                                                        class="form-select form-select-sm border-0 bg-white rounded-pill px-3 mt-1">
                                                        <?= Mod::menuList($val); ?>
                                                    </select>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Custom Resource -->
                                    <div class="col-md-6">
                                        <div
                                            class="card border-0 bg-light rounded-4 p-3 h-100 <?= ($data['menus'][0]->type == 'custom') ? 'border-start border-4 border-primary shadow-sm' : '' ?>">
                                            <div class="form-check d-flex align-items-start gap-2">
                                                <input type="radio" name='type' id="typeCustom"
                                                    class="form-check-input mt-1" value="custom"
                                                    <?= ($data['menus'][0]->type == 'custom') ? 'checked' : '' ?>>
                                                <label class="form-check-label flex-grow-1" for="typeCustom">
                                                    <div class="fw-bold text-dark small"><?= _("Custom Endpoint"); ?>
                                                    </div>
                                                    <div class="extra-small text-muted mb-2">
                                                        <?= _("Link to external resources."); ?>
                                                    </div>
                                                    <?php
                                                    $val = ($data['menus'][0]->type == 'custom') ? $data['menus'][0]->value : '';
                                                    ?>
                                                    <input
                                                        class="form-control form-control-sm border-0 bg-white rounded-pill px-3 mt-1"
                                                        name="custom" value="<?= $val; ?>" placeholder="https://...">
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="token" value="<?= $token; ?>">
</form>