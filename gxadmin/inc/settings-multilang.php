<?php
/**
 * GeniXCMS - Content Management Systemhouse.
 *
 * PHP Based Content Management System and Framework
 */

// ── PREPARE LANGUAGE LIST HTML ───────────────────────────────────
$langCardsHtml = '<div class="row g-3">';
if ($data['list_lang'] != "" && count($data['list_lang']) > 0) {
    foreach ($data['list_lang'] as $key => $value) {
        $flag = strtolower($value['flag']);
        $langCardsHtml .= '
            <div class="col-sm-6 col-md-4 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 transition-hover border-start border-4 border-light">
                    <div class="card-body p-3 d-flex align-items-center gap-3">
                        <div class="fs-4">
                            <span class="flag-icon flag-icon-'.$flag.' rounded shadow-sm"></span>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark fs-7 lh-1 mb-1">'.$value['country'].'</div>
                            <div class="extra-small text-muted fw-black text-uppercase tracking-widest" style="font-size:0.6rem;">'.$key.'</div>
                        </div>
                        <a href="index.php?page=multilang&del='.$key.'&token='.TOKEN.'" class="btn btn-light btn-sm rounded-circle d-flex align-items-center justify-content-center border-0 p-2 hover-danger" title="'._("Remove").'">
                            <i class="bi bi-trash3 text-danger fs-8"></i>
                        </a>
                    </div>
                </div>
            </div>';
    }
} else {
    $langCardsHtml .= '
        <div class="col-12">
            <div class="text-center py-5 bg-light rounded-5 border-2 border-dashed">
                <i class="bi bi-translate fs-1 text-muted opacity-25"></i>
                <div class="text-muted small fw-bold mt-2">'._("No secondary languages configured yet.")."</div>
            </div>
        </div>";
}
$langCardsHtml .= '</div>';

// ── PREPARE OPTIONS HTML ──────────────────────────────────────────
$defaultLangOptions = '';
if (is_array($data['list_lang'])) {
    foreach ($data['list_lang'] as $key => $value) {
        $sel = ($key == $data['default_lang']) ? 'selected' : '';
        $defaultLangOptions .= "<option value=\"{$key}\" $sel>{$value['country']}</option>";
    }
}

// ── PREPARE SCHEMA ────────────────────────────────────────────────
$schema = [
    'header' => [
        'title' => _('Global Localization'),
        'subtitle' => _('Manage multiple language translations and regional content variations for your global audience.'),
        'icon' => 'bi bi-globe-americas',
        'button' => [
            'type' => 'button', 'label' => _('New Language'), 'icon' => 'bi bi-plus-lg', 'btn_type' => 'button',
            'class' => 'btn btn-primary rounded-pill px-4 shadow-sm fw-bold', 'attr' => 'data-bs-toggle="modal" data-bs-target="#addcountry"'
        ],
    ],
    'card_wrapper' => false,
    'content' => [
        ['type' => 'row', 'items' => [
            ['width' => 12, 'content' => [
                ['type' => 'card', 'body_elements' => [
                    ['type' => 'row', 'items' => [
                        ['width' => 6, 'content' => [
                            ['type' => 'raw', 'html' => '
                                <div class="form-check form-switch bg-light rounded-4 p-4 ps-5 border-start border-4 border-primary shadow-none h-100 d-flex align-items-center">
                                    <input class="form-check-input" type="checkbox" name="multilang_enable" id="enableMulti" '.(Options::v('multilang_enable') === 'on' ? 'checked' : '').'>
                                    <label class="form-check-label ps-3" for="enableMulti">
                                        <div class="fw-black text-dark text-uppercase tracking-wider extra-small mb-1">'._("Multi-language Status").'</div>
                                        <div class="small text-muted fw-bold">'._("Enable localized content delivery architecture.").'</div>
                                    </label>
                                </div>']
                        ]],
                        ['width' => 6, 'content' => [
                            ['type' => 'raw', 'html' => '
                                <label class="form-label fw-black text-muted extra-small text-uppercase tracking-wider" style="font-size:0.65rem;">'._("Primary Master Dialect").'</label>
                                <select name="multilang_default" class="form-select border bg-light rounded-4 py-3 px-3 shadow-none fs-8 fw-bold">
                                    '.$defaultLangOptions.'
                                </select>']
                        ]]
                    ]]
                ], 'footer' => '<button type="submit" name="change" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold mb-n1">'._("Save Global Architecture").'</button>']
            ]]
        ]],
        ['type' => 'heading', 'text' => _('Configured Languages'), 'icon' => 'bi bi-translate', 'subtitle' => _('Independent translation protocols currently active in the ecosystem.'), 'class' => 'fw-black text-dark text-uppercase tracking-widest fs-8 mb-4 mt-4 ms-2'],
        ['type' => 'raw', 'html' => $langCardsHtml]
    ]
];

// ── RENDER ────────────────────────────────────────────────────────
echo '<div class="col-md-12">';
echo Hooks::run('admin_page_notif_action', $data);
echo '</div>';

echo '<form action="index.php?page=settings-multilang" method="post">';
$builder = new UiBuilder($schema);
$builder->render();
echo '<input type="hidden" name="token" value="'.TOKEN.'">';
echo '</form>';
?>

<style>
    .transition-hover { transition: all 0.2s ease; }
    .transition-hover:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; border-color: var(--bs-primary) !important; }
    .hover-danger:hover { background: #fee2e2 !important; }
</style>

<!-- Modal remains outside for standard Bootstrap behavior -->
<div class="modal fade" id="addcountry" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-5 overflow-hidden">
            <form action="index.php?page=multilang" method="post">
                <div class="modal-header border-0 py-4 px-5">
                    <h5 class="modal-title fw-black text-dark text-uppercase tracking-widest fs-7"><?=_("Language Architect");?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5 pt-0">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label fw-black text-muted extra-small text-uppercase tracking-wider" style="font-size:0.65rem;"><?=_("Visual Display Name");?></label>
                            <input type="text" name='multilang_country_name' class="form-control border bg-light rounded-4 py-3 px-3 shadow-none fs-8 fw-bold" placeholder="e.g. English">
                            <div class="extra-small text-muted mt-2 fw-medium"><?=_("Human readable name for this localization.");?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-black text-muted extra-small text-uppercase tracking-wider" style="font-size:0.65rem;"><?=_("ISO Identifier");?></label>
                            <input type="text" name="multilang_country_code" class="form-control border bg-light rounded-4 py-3 px-3 shadow-none fs-8 fw-bold" placeholder="en">
                            <div class="extra-small text-muted mt-2 fw-medium"><?=_("Lowercase (e.g. en, id).");?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-black text-muted extra-small text-uppercase tracking-wider" style="font-size:0.65rem;"><?=_("Flag Icon");?></label>
                            <select name="multilang_country_flag" class="form-select border bg-light rounded-4 py-3 px-3 shadow-none fs-8 fw-bold">
                                <?=Date::optCountry();?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-black text-muted extra-small text-uppercase tracking-wider" style="font-size:0.65rem;"><?=_("System Mapping Protocol");?></label>
                            <select name="multilang_system_lang" class="form-select border bg-light rounded-4 py-3 px-3 shadow-none fs-8 fw-bold">
                                <?=Language::optDropdown();?>
                            </select>
                            <div class="extra-small text-muted mt-2 fw-medium"><?=_("Maps to internal system translation files.");?></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-5 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal"><?=_("Cancel");?></button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" name="addcountry"><?=_("Create Dialect");?></button>
                </div>
                <input type="hidden" name="token" value="<?=TOKEN;?>">
            </form>
        </div>
    </div>
</div>
