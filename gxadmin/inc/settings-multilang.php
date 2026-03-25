<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 */
?>
<form action="index.php?page=settings-multilang" method="post">
    <div class="col-md-12">
        <?=Hooks::run('admin_page_notif_action', $data);?>
        <?=Hooks::run('admin_page_top_action', $data);?>
    </div>

    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="row align-items-center mb-4">
            <div class="col-md-6 text-start">
                <h3 class="fw-bold text-dark mb-0"><?=_("Global Localization");?></h3>
                <p class="text-muted small mb-0"><?=_("Manage multiple language translations and regional content variations.");?></p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <div class="btn-group gap-2">
                    <button type="submit" name="change" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="bi bi-save me-1"></i> <?=_("Save Architecture");?>
                    </button>
                    <button type="button" class="btn btn-outline-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addcountry">
                        <i class="bi bi-plus-circle me-1"></i> <?=_("Add Language");?>
                    </button>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Global Config -->
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="row align-items-center g-4">
                            <div class="col-md-6">
                                <div class="form-check form-switch bg-light rounded-4 p-3 ps-5 border-start border-4 border-primary shadow-none h-100">
                                    <input class="form-check-input" type="checkbox" name="multilang_enable" id="enableMulti" <?= (Options::v('multilang_enable') === 'on') ? 'checked' : ''; ?>>
                                    <label class="form-check-label ps-2" for="enableMulti">
                                        <div class="fw-bold text-dark"><?=_("Multilanguage Engine Status");?></div>
                                        <div class="extra-small text-muted"><?=_("Enable or disable localized content delivery across the platform.");?></div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Primary Default Language");?></label>
                                <select name="multilang_default" class="form-select border-0 bg-light rounded-3 py-2 shadow-none">
                                    <?php
                                    if ( is_array($data['list_lang']) ):
                                        foreach ($data['list_lang'] as $key => $value) {
                                            $sel = ($key == $data['default_lang']) ? 'selected' : '';
                                            echo "<option value=\"{$key}\" $sel>{$value['country']}</option>";
                                        }
                                    endif;
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Language Cards -->
            <div class="col-lg-12">
                <h6 class="fw-bold text-dark mb-4 ms-2"><?=_("Configured Dialects");?></h6>
                <div class="row g-3">
                    <?php
                    if ($data['list_lang'] != "" && count($data['list_lang']) > 0) {
                        $list_lang = $data['list_lang'];
                        foreach ($list_lang as $key => $value) {
                            $flag = strtolower($value['flag']);
                            ?>
                            <div class="col-sm-6 col-md-4 col-xl-3">
                                <div class="card border-0 shadow-sm rounded-4 h-100 transition-hover">
                                    <div class="card-body p-3 d-flex align-items-center gap-3">
                                        <div class="fs-4">
                                            <span class="flag-icon flag-icon-<?=$flag;?> rounded"></span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold text-dark fs-7 lh-1 mb-1"><?=$value['country'];?></div>
                                            <div class="extra-small text-muted fw-bold text-uppercase"><?=$key;?></div>
                                        </div>
                                        <a href="index.php?page=multilang&del=<?=$key;?>&token=<?=TOKEN;?>" class="btn btn-light btn-sm rounded-circle d-flex align-items-center justify-content-center border-0 p-2" title="<?=_("Remove");?>">
                                            <i class="bi bi-trash3 text-danger fs-8"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        ?>
                        <div class="col-12">
                            <div class="text-center py-5 bg-light rounded-4 border-2 border-dashed">
                                <i class="bi bi-translate fs-1 text-muted opacity-25"></i>
                                <div class="text-muted small mt-2"><?=_("No secondary languages configured yet.");?></div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="token" value="<?=TOKEN;?>">
</form>

<!-- Add Language Modal -->
<div class="modal fade" id="addcountry" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="index.php?page=multilang" method="post">
                <div class="modal-header bg-primary py-3 px-4">
                    <h5 class="modal-title fw-bold text-white mb-0"><?=_("Language Architect");?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Display Name");?></label>
                            <input type="text" name='multilang_country_name' class="form-control border-0 bg-light rounded-3 py-2 shadow-none" placeholder="e.g. English">
                            <div class="extra-small text-muted mt-1"><?=_("Human readable name for this localization.");?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase"><?=_("ISO Code");?></label>
                            <input type="text" name="multilang_country_code" class="form-control border-0 bg-light rounded-3 py-2 shadow-none" placeholder="en">
                            <div class="extra-small text-muted mt-1"><?=_("Lowercase identifier (e.g. en, id, jp).");?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Flag Icon");?></label>
                            <select name="multilang_country_flag" class="form-select border-0 bg-light rounded-3 py-2 shadow-none">
                                <?=Date::optCountry();?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Mapping System Language");?></label>
                            <select name="multilang_system_lang" class="form-select border-0 bg-light rounded-3 py-2 shadow-none">
                                <?=Language::optDropdown();?>
                            </select>
                            <div class="extra-small text-muted mt-1"><?=_("Maps this localization to internal system translation files.");?></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal"><?=_("Cancel");?></button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm" name="addcountry"><?=_("Create Dialect");?></button>
                </div>
                <input type="hidden" name="token" value="<?=TOKEN;?>">
            </form>
        </div>
    </div>
</div>
