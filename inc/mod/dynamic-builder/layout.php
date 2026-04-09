<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');

$modUrl = rtrim(Site::$url, '/') . '/inc/mod/dynamic-builder/';
$blocks = DynamicBuilder::getBlocks();
$config = [
    'siteUrl' => rtrim(Site::$url, '/') . '/',
    'isSmartUrl' => SMART_URL ? true : false,
    'apiEndpoint' => Url::ajax('api', ['action' => 'recent_posts', 'num' => 3]),
];
?>
<div class="modal fade" id="builderModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content border-0 shadow-none">
            <div class="modal-header bg-dark text-white border-0 py-2 shadow-sm rounded-0">
                <div class="container-fluid d-flex align-items-center justify-content-between p-0 px-2 pb-1">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white p-1 rounded-3 me-2 d-flex align-items-center justify-content-center"
                            style="width: 32px; height: 32px;">
                            <i class="bi bi-layers-half fs-5"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold m-0" style="letter-spacing: 0.5px;">DYNAMIC BUILDER</h6>
                            <span class="text-white-50" style="font-size: 10px; text-transform: uppercase;">PREMIUM
                                VISUAL EDITOR</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-center flex-grow-1 mx-5">
                        <div class="gjs-pn-devices-c btn-group bg-black bg-opacity-25 rounded-pill p-1"></div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm"
                            id="save-builder-page">
                            <i class="bi bi-cloud-arrow-up-fill me-1"></i> Sync & Exit
                        </button>
                        <button type="button"
                            class="btn btn-outline-light btn-sm rounded-circle p-1 d-flex align-items-center justify-content-center"
                            style="width: 30px; height: 30px;" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Breadcrumb Navigation Bar -->
            <div id="gjs-breadcrumb"
                class="bg-black bg-opacity-25 border-bottom border-white border-opacity-10 px-3 py-1 d-flex align-items-center gap-2"
                style="height: 35px; overflow-x: auto; white-space: nowrap; font-family: 'Plus Jakarta Sans', sans-serif; z-index: 50;">
                <span class="text-white-50" style="font-size: 11px;"><i class="bi bi-chevron-double-right me-1"></i>
                    Selection:</span>
                <div id="breadcrumb-list" class="d-flex align-items-center gap-1">
                    <span class="badge bg-secondary opacity-25">Select an element...</span>
                </div>
            </div>
            <div class="modal-body p-0 position-relative">
                <div id="gjs"></div>

                <!-- Grid Creator Static Bottom Bar -->
                <div id="grid-creator-container" class="border-top border-white border-opacity-10"
                    style="background: #0a111e;">
                    <!-- Grid Picker Panel (hidden by default, above canvas zone only) -->
                    <div id="grid-picker-box" class="px-4 py-3 border-bottom border-white border-opacity-10 d-none" style="background: #0d1829; margin-left: 320px;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-white-50 fw-bold" style="font-size: 11px; letter-spacing: 0.08em;"><i
                                    class="bi bi-grid-1x2 me-2 text-primary"></i>SELECT GRID LAYOUT</span>
                            <button type="button" class="btn btn-sm p-0 border-0 text-white-50" id="close-grid-picker"
                                style="background: none; font-size: 12px;"><i class="bi bi-x-lg"></i></button>
                        </div>
                        <div class="row g-2" id="grid-options-list"></div>
                    </div>

                    <!-- Two-pane bottom bar: Left (sidebar zone) + Right (canvas zone) -->
                    <div class="d-flex" style="height: 36px;">
                        <!-- Left zone: matches sidebar width, reserved for future menus -->
                        <div id="grid-bar-left" class="d-flex align-items-center justify-content-center border-end border-white border-opacity-10" style="width: 320px; min-width: 320px; flex-shrink: 0;">
                            <span class="text-white-50" style="font-size: 9px; letter-spacing: 0.08em; opacity: 0.4;">SIDEBAR</span>
                        </div>
                        <!-- Right zone: canvas area (position relative for picker anchor) -->
                        <div class="d-flex align-items-center justify-content-center flex-grow-1 position-relative">
                            <!-- Grid Picker Panel - anchored inside canvas zone -->
                            <div id="grid-picker-box" class="px-4 py-3 border border-white border-opacity-10 d-none" style="background: #0d1829;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-white-50 fw-bold" style="font-size: 11px; letter-spacing: 0.08em;"><i class="bi bi-grid-1x2 me-2 text-primary"></i>SELECT GRID LAYOUT</span>
                                    <button type="button" class="btn btn-sm p-0 border-0 text-white-50" id="close-grid-picker" style="background: none; font-size: 12px;"><i class="bi bi-x-lg"></i></button>
                                </div>
                                <div class="row g-2" id="grid-options-list"></div>
                            </div>

                            <button type="button" id="toggle-grid-picker"
                                class="btn btn-sm d-flex align-items-center gap-2 px-3 py-1 rounded-pill fw-bold"
                                style="background: rgba(37,99,235,0.15); border: 1px solid rgba(37,99,235,0.4); color: #93c5fd; font-size: 11px; transition: all 0.2s;">
                                <i class="bi bi-plus-lg"></i> Add Row
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.dynamicBuilderConfig = <?php echo json_encode($config, JSON_UNESCAPED_SLASHES); ?>;
    window.dynamicBuilderBlocks = <?php echo json_encode($blocks, JSON_UNESCAPED_SLASHES); ?>;
</script>