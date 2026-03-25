<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 */
defined('GX_LIB') or die('Direct Access Not Allowed!');
?>
<form action="index.php?page=settings-cache" method="post">
    <div class="col-md-12">
        <?=Hooks::run('admin_page_notif_action', $data);?>
        <?=Hooks::run('admin_page_top_action', $data);?>
    </div>

    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="row align-items-center mb-4">
            <div class="col-md-6 text-start">
                <h3 class="fw-bold text-dark mb-0"><?=_("Persistence Layer");?></h3>
                <p class="text-muted small mb-0"><?=_("Manage temporary data storage to boost platform performance and responsiveness.");?></p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <div class="btn-group gap-2">
                    <button type="submit" name="change" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="bi bi-save me-1"></i> <?=_("Apply Persistence");?>
                    </button>
                    <button type="reset" class="btn btn-light border rounded-pill px-4">
                        <?=_("Discard");?>
                    </button>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <h6 class="fw-bold text-success text-uppercase mb-4"><?=_("Cache Engine");?></h6>
                        <div class="row g-4">
                            <!-- Activation -->
                            <div class="col-md-4">
                                <div class="form-check form-switch bg-light rounded-4 p-3 ps-5 border-start border-4 border-success shadow-none h-100">
                                    <input class="form-check-input" type="checkbox" name="cache_enabled" id="enableCache" <?= ($data['cache_enabled'] === 'on') ? 'checked' : ''; ?>>
                                    <label class="form-check-label ps-2" for="enableCache">
                                        <div class="fw-bold text-dark"><?=_("Enable Object Caching");?></div>
                                        <div class="extra-small text-muted"><?=_("Store pre-compiled data to minimize database hits.");?></div>
                                    </label>
                                </div>
                            </div>

                            <!-- Engine Type -->
                            <div class="col-md-2">
                                <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Engine Type");?></label>
                                <select name="cache_type" class="form-select border-0 bg-light rounded-3 py-2 px-3 shadow-none">
                                    <option value="file" <?=($data['cache_type'] == 'file') ? 'selected':'';?>>File Cache</option>
                                    <option value="redis" <?=($data['cache_type'] == 'redis') ? 'selected':'';?>>Redis Cache</option>
                                </select>
                            </div>

                            <!-- Parameters -->
                            <div class="col-md-6">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Lifespan");?></label>
                                        <div class="input-group">
                                            <input type="number" name="cache_timeout" class="form-control border-0 bg-light rounded-start-3 py-2 shadow-none" value="<?=$data['cache_timeout'];?>">
                                            <span class="input-group-text border-0 bg-light rounded-end-3 py-1 opacity-50 px-3 fs-8 text-uppercase">sec</span>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Registry Path (File Cache)");?></label>
                                        <input type="text" class="form-control border-0 bg-light rounded-3 py-2 px-3 shadow-none" name="cache_path" value="<?=$data['cache_path'];?>">
                                        <div class="extra-small text-danger mt-1 ps-1 fw-bold"><i class="bi bi-exclamation-triangle me-1"></i><?=_("Ensure permissions are set to 777.");?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Redis Configuration -->
                        <div id="redis-config" class="mt-4 border-top pt-4" style="display: <?=($data['cache_type'] == 'redis' ? 'block' : 'none');?>">
                             <h6 class="fw-bold text-primary extra-small text-uppercase mb-3"><?=_("Redis Connection Details");?></h6>
                             <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Redis Host");?></label>
                                    <input type="text" class="form-control border-0 bg-light rounded-3 py-2 px-3 shadow-none" name="redis_host" value="<?=$data['redis_host'];?>" placeholder="127.0.0.1">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Redis Port");?></label>
                                    <input type="number" class="form-control border-0 bg-light rounded-3 py-2 px-3 shadow-none" name="redis_port" value="<?=$data['redis_port'];?>" placeholder="6379">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Redis Password");?></label>
                                    <input type="password" class="form-control border-0 bg-light rounded-3 py-2 px-3 shadow-none" name="redis_pass" value="<?=$data['redis_pass'];?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Redis DB Index");?></label>
                                    <input type="number" class="form-control border-0 bg-light rounded-3 py-2 px-3 shadow-none" name="redis_db" value="<?=$data['redis_db'];?>" placeholder="0">
                                </div>
                             </div>
                        </div>

                        <!-- Info Alert -->
                        <div class="mt-4 border-top pt-4">
                            <div class="d-flex gap-3 align-items-start bg-light p-3 rounded-4">
                                <i class="bi bi-speedometer2 fs-3 text-info"></i>
                                <div>
                                    <div class="fw-bold text-dark fs-7"><?=_("Multi-Backend Strategy");?></div>
                                    <div class="extra-small text-muted"><?=_("Redis is recommended for high-traffic sites to reduce disk I/O. File cache is best for shared hosting without Redis support.");?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        document.querySelector('select[name="cache_type"]').addEventListener('change', function() {
            document.getElementById('redis-config').style.display = (this.value === 'redis') ? 'block' : 'none';
        });
        </script>
    </div>
    <input type="hidden" name="token" value="<?=TOKEN;?>">
</form>
