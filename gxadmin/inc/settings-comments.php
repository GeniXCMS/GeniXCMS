<?php
/**
 * GeniXCMS - Content Management Systemhouse.
 *
 * PHP Based Content Management System and Framework
 */

// ── PREPARE SCHEMA ────────────────────────────────────────────────
$schema = [
    'header' => [
        'title' => _('Engagement Settings'),
        'subtitle' => _('Manage user interactions, comment moderation, and anti-spam protocols.'),
        'icon' => 'bi bi-chat-quote',
        'button' => [
            'type' => 'button', 'name' => 'change', 'label' => _('Save Setup'), 'icon' => 'bi bi-save',
            'class' => 'btn btn-primary rounded-pill px-4 shadow-sm fw-bold', 'attr' => 'value="Change"'
        ],
    ],
    'card_wrapper' => false,
    'content' => [
        ['type' => 'row', 'items' => [
            ['width' => 12, 'content' => [
                ['type' => 'card', 'body_elements' => [
                    ['type' => 'row', 'items' => [
                        ['width' => 4, 'content' => [
                            ['type' => 'raw', 'html' => '
                                <div class="form-check form-switch bg-light rounded-4 p-2 ps-5 border-start border-4 border-success shadow-none h-100 d-flex align-items-center">
                                    <input class="form-check-input" type="checkbox" name="comments_enable" id="enableComm" '.($data['comments_enable'] === 'on' ? 'checked' : '').'>
                                    <label class="form-check-label ps-2" for="enableComm">
                                        <div class="fw-black text-dark text-uppercase tracking-wider fs-9 mb-0">'._("Ecosystem Mode").'</div>
                                        <div class="extra-small text-muted fw-bold">'._("Enable Live Comments").'</div>
                                    </label>
                                </div>']
                        ]],
                        ['width' => 4, 'content' => [
                            ['type' => 'raw', 'html' => '
                                <div class="p-2 bg-light rounded-4 border h-100 d-flex align-items-center gap-3 px-3">
                                    <div class="flex-fill">
                                        <label class="form-label fw-black text-muted text-uppercase tracking-wider mb-0" style="font-size:0.55rem;">'._("Pagination Hub").'</label>
                                        <input type="number" name="comments_perpage" class="form-control border bg-white rounded-3 py-0 fs-8 fw-bold px-2" value="'.$data['comments_perpage'].'" style="height:28px;">
                                    </div>
                                    <div class="extra-small text-muted fw-bold text-uppercase opacity-50 pt-2">'._("Entries").'</div>
                                </div>']
                        ]],
                        ['width' => 4, 'content' => [
                            ['type' => 'raw', 'html' => '
                                <div class="p-2 bg-light rounded-4 border-start border-4 border-info h-100 d-flex align-items-center px-3">
                                    <div class="rounded-circle bg-info bg-opacity-10 p-2 me-3"><i class="bi bi-shield-lock text-info fs-7"></i></div>
                                    <div>
                                        <div class="fw-black text-dark text-uppercase tracking-wider fs-9 mb-0">'._("Spam Filter").'</div>
                                        <div class="extra-small text-muted fw-bold">'._("Active Guard").'</div>
                                    </div>
                                </div>']
                        ]]
                    ]]
                ]]
            ]]
        ]],
        ['type' => 'row', 'items' => [
            ['width' => 8, 'content' => [
                ['type' => 'card', 'title' => _('Spam Registry'), 'icon' => 'bi bi-shield-slash', 'body_elements' => [
                    ['type' => 'raw', 'html' => '
                        <label class="form-label fw-black text-muted extra-small text-uppercase tracking-wider mb-2" style="font-size:0.6rem;">'._("Blacklisted Records").'</label>
                        <textarea class="form-control border bg-light rounded-4 shadow-none p-4 fs-8 font-monospace fw-bold" name="spamwords" rows="8" placeholder="restricted-term-1&#10;restricted-term-2">'.$data['spamwords'].'</textarea>
                        <div class="extra-small text-muted mt-2 fw-medium"><i class="bi bi-info-circle me-1"></i> '._("List one restricted keyword or pattern per line for automated filtration.").'</div>']
                ]]
            ]],
            ['width' => 4, 'content' => [
                ['type' => 'alert', 'style' => 'primary', 'content' => '
                    <h6 class="fw-black text-primary text-uppercase fs-8 mb-3 tracking-widest border-bottom pb-2 mt-n1"><i class="bi bi-cpu-fill me-2"></i>'._("Moderation Logic").'</h6>
                    <div class="d-flex flex-column gap-3">
                        <div class="extra-small text-dark fw-bold lh-base py-1 px-3 bg-white bg-opacity-50 rounded-3 border-start border-3 border-primary">'._("Audit blacklist protocols weekly for community health.").'</div>
                        <div class="extra-small text-dark fw-bold lh-base py-1 px-3 bg-white bg-opacity-50 rounded-3 border-start border-3 border-primary">'._("Keep pagination low during high-traffic events.").'</div>
                        <div class="extra-small text-dark fw-bold lh-base py-1 px-3 bg-white bg-opacity-50 rounded-3 border-start border-3 border-primary">'._("Use exact pattern matching for restricted entries.").'</div>
                    </div>']
            ]]
        ]]
    ]
];

// ── RENDER ────────────────────────────────────────────────────────
echo '<div class="col-md-12">';
echo Hooks::run('admin_page_notif_action', $data);
echo '</div>';

echo '<form action="index.php?page=settings-comments" method="post">';
$builder = new UiBuilder($schema);
$builder->render();
echo '<input type="hidden" name="token" value="'.TOKEN.'">';
echo '</form>';
?>
