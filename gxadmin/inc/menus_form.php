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


if (isset($_GET['id'])) {
    $menuid = Typo::cleanX($_GET['id']);
} else {
    $menuid = $data['menuid'];
}
?>
<form action="" method="POST">
    <div class="row align-items-center mb-4">
        <div class="col-8">
            <h5 class="fw-bold text-dark m-0"><?= _("Integrate New Link"); ?></h5>
        </div>
        <div class="col-4 text-end">
            <div class="btn-group gap-2">
                <button type="submit" name="additem" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                    <i class="bi bi-save me-1"></i> <?= _("Submit"); ?>
                </button>
                <button type="reset" class="btn btn-light btn-sm border rounded-pill px-3">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </button>
            </div>
        </div>
    </div>

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
                            echo "<option value=\"$p->id\">$p->name</option>";
                            foreach ($data['parent'] as $p2) {
                                if ($p2->parent == $p->id) {
                                    echo "<option value=\"$p2->id\">&nbsp;&nbsp;&nbsp;$p2->name</option>";
                                    foreach ($data['parent'] as $p3) {
                                        if ($p3->parent == $p2->id) {
                                            echo "<option value=\"$p3->id\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$p3->name</option>";
                                        }
                                    }
                                }
                            }
                        }
                    }
                    ?>
                </select>
                <div class="extra-small text-muted mt-1"><?= _("Select the depth level for this menu item."); ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted text-uppercase"><?= _("Registry ID"); ?></label>
                <input type="text" name='id' class="form-control border-0 bg-white rounded-3 py-2 px-3 shadow-none"
                    value="<?= $menuid; ?>" readonly>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted text-uppercase"><?= _("CSS Class"); ?></label>
                <input type="text" name='class' class="form-control border-0 bg-light rounded-3 py-2 px-3"
                    placeholder="nav-item">
            </div>
        </div>

        <div class="col-md-12">
            <div class="mb-4">
                <label class="form-label small fw-bold text-muted text-uppercase"><?= _("Label Text"); ?></label>
                <input type="text" name='name' class="form-control border-0 bg-light rounded-4 py-3 px-4 fw-bold fs-5"
                    placeholder="<?= _("Friendly Name..."); ?>" required>
                <div class="extra-small text-muted mt-1 ps-2"><?= _("This text will be displayed to your visitors."); ?>
                </div>
            </div>
        </div>

        <!-- Menu Type Selection -->
        <div class="col-12 mt-2">
            <h6 class="fw-bold text-dark extra-small text-uppercase tracking-widest mb-3 border-bottom pb-2">
                <?= _("Redirect Target Type"); ?>
            </h6>
            <div class="row g-3">
                <!-- Internal Page -->
                <div class="col-md-6">
                    <div class="card border-0 bg-light rounded-4 p-3 h-100">
                        <div class="form-check d-flex align-items-start gap-2">
                            <input type="radio" name='type' id="typePage" class="form-check-input mt-1" value="page">
                            <label class="form-check-label flex-grow-1" for="typePage">
                                <div class="fw-bold text-dark small"><?= _("Internal Page"); ?></div>
                                <div class="extra-small text-muted mb-2">
                                    <?= _("Connect to an existing static page."); ?>
                                </div>
                                <?php
                                $vars = array(
                                    'name' => 'page',
                                    'type' => 'page',
                                    'sort' => 'ASC',
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
                    <div class="card border-0 bg-light rounded-4 p-3 h-100">
                        <div class="form-check d-flex align-items-start gap-2">
                            <input type="radio" name='type' id="typeCat" class="form-check-input mt-1" value="cat">
                            <label class="form-check-label flex-grow-1" for="typeCat">
                                <div class="fw-bold text-dark small"><?= _("Category View"); ?></div>
                                <div class="extra-small text-muted mb-2">
                                    <?= _("Link to a specific category archive."); ?>
                                </div>
                                <?php
                                $vars = array(
                                    'name' => 'cat',
                                    'sort' => 'ASC',
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
                    <div class="card border-0 bg-light rounded-4 p-3 h-100">
                        <div class="form-check d-flex align-items-start gap-2">
                            <input type="radio" name='type' id="typeMod" class="form-check-input mt-1" value="mod">
                            <label class="form-check-label flex-grow-1" for="typeMod">
                                <div class="fw-bold text-dark small"><?= _("System Module"); ?></div>
                                <div class="extra-small text-muted mb-2">
                                    <?= _("Direct access to installed modular features."); ?>
                                </div>
                                <select name="mod"
                                    class="form-select form-select-sm border-0 bg-white rounded-pill px-3 mt-1">
                                    <?= Mod::menuList(); ?>
                                </select>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Custom Resource -->
                <div class="col-md-6">
                    <div class="card border-0 bg-light rounded-4 p-3 h-100">
                        <div class="form-check d-flex align-items-start gap-2">
                            <input type="radio" name='type' id="typeCustom" class="form-check-input mt-1"
                                value="custom">
                            <label class="form-check-label flex-grow-1" for="typeCustom">
                                <div class="fw-bold text-dark small"><?= _("Custom Endpoint"); ?></div>
                                <div class="extra-small text-muted mb-2">
                                    <?= _("Link to an external site or custom URL."); ?>
                                </div>
                                <input class="form-control form-control-sm border-0 bg-white rounded-pill px-3 mt-1"
                                    name="custom" placeholder="https://...">
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="token" value="<?= TOKEN; ?>">
</form>