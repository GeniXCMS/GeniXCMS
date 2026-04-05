<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 */

// ── PREPARE DATA ──────────────────────────────────────────────────
$rows = [];
if ($data['num'] > 0) {
    foreach ($data['posts'] as $p) {
        $pObj = (object)$p;
        $status = '';
        $rowClass = '';
        if ($pObj->status == '0') {
            $status = ['c' => 'secondary', 'l' => _("Hidden")];
            $rowClass = 'opacity-75';
        } elseif ($pObj->status == '1') {
            $status = ['c' => 'success', 'l' => _("Approved")];
        } elseif ($pObj->status == '2') {
            $status = ['c' => 'warning', 'l' => _("Pending")];
            $rowClass = 'bg-warning bg-opacity-10';
        }
        
        $commentText = Typo::strip($pObj->comment);
        $commentShort = (strlen($commentText) > 120) ? substr($commentText, 0, 117).'...' : $commentText;
        $commentUrl = isset($data['posts'][0]) && is_object($data['posts'][0]) ? Url::post($pObj->post_id): '#';

        $rows[] = [
            ['content' => "<input type='checkbox' name='post_id[]' value='{$pObj->id}' class='check form-check-input shadow-none border'>", 'class' => 'ps-4'],
            "<div>
                <a href='{$commentUrl}' target='_blank' class='text-dark fw-bold text-decoration-none h6 mb-0 d-inline-block ls-n1'>{$commentShort}</a>
                <div class='extra-small text-muted d-flex align-items-center mt-1'>
                    <i class='bi bi-geo-alt me-1'></i> IP: {$pObj->ipaddress}
                    <span class='mx-2 opacity-25'>|</span>
                    <i class='bi bi-link-45deg me-1'></i> ID: {$pObj->id}
                </div>
            </div>",
            ['content' => "<div class='fw-bold text-dark small mb-0'>{$pObj->name}</div><div class='extra-small text-muted'>{$pObj->email}</div>", 'class' => 'text-center'],
            ['content' => "<div class='small fw-bold text-dark mb-0'>".Date::format($pObj->date, 'd M Y')."</div><div class='text-muted extra-small'>".Date::format($pObj->date, 'H:i A')."</div>", 'class' => 'text-center'],
            ['content' => "<span class='badge bg-{$status['c']} bg-opacity-10 text-{$status['c']} px-3 rounded-pill fw-bold text-uppercase' style='font-size: 0.65rem;'>{$status['l']}</span>", 'class' => 'text-center'],
            ['content' => "
                <a href='index.php?page=comments&act=del&id={$pObj->id}&token=".TOKEN."' class='btn btn-light btn-sm rounded-circle border' onclick=\"return confirm('"._("Permanent removal of this comment?")."');\" title='Remove Permanently'>
                    <i class='bi bi-trash text-danger'></i>
                </a>", 'class' => 'text-end pe-4']
        ];
    }
}

// ── DEFINE UI SCHEMA ──────────────────────────────────────────────
$schema = [
    'header' => [
        'title' => _('Engagement Audit'),
        'subtitle' => _('Review and moderate user interactions across your digital assets.'),
        'icon' => 'bi bi-chat-dots',
    ],
    'content' => [
        ['type' => 'stat_cards', 'size' => 'small', 'items' => [
            ['label' => _('Total Feed'), 'value' => (string)Stats::totalComments(), 'icon' => 'bi bi-chat-dots', 'color' => 'primary'],
            ['label' => _('Active'), 'value' => (string)Stats::activeComments(), 'icon' => 'bi bi-patch-check', 'color' => 'success'],
            ['label' => _('Pending'), 'value' => (string)Stats::pendingComments(), 'icon' => 'bi bi-hourglass-split', 'color' => 'warning'],
            ['label' => _('Spam/Blocked'), 'value' => (string)Stats::inactiveComments(), 'icon' => 'bi bi-shield-exclamation', 'color' => 'danger']
        ]],
        [
            'type' => 'card',
            'title' => _('Message Queue'),
            'icon' => 'bi bi-chat-left-text-fill',
            'no_padding' => true,
            'header_action' => '
                <form action="index.php?page=comments" method="get" class="d-flex gap-2 flex-wrap justify-content-end align-items-center">
                    <input type="hidden" name="page" value="comments">
                    <div class="input-group input-group-sm w-auto shadow-sm rounded-pill overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-3"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control border-0 ps-1 bg-white" placeholder="'._("Keyword...").'" style="width:140px;" value="'.($_GET['q'] ?? '').'">
                    </div>
                    <div class="d-flex gap-1 align-items-center bg-white border rounded-pill px-2 shadow-sm">
                        <i class="bi bi-calendar-range text-muted ms-1" style="font-size:0.75rem;"></i>
                        <input type="date" name="from" class="form-control form-control-sm border-0 bg-transparent p-1" style="font-size:0.75rem; width:110px;" value="'.($_GET['from'] ?? '').'" title="'._("Received From").'">
                        <span class="text-muted small">-</span>
                        <input type="date" name="to" class="form-control form-control-sm border-0 bg-transparent p-1" style="font-size:0.75rem; width:110px;" value="'.($_GET['to'] ?? '').'" title="'._("Received To").'">
                    </div>
                    <select name="status" class="form-select form-select-sm rounded-pill px-3 shadow-none border bg-white shadow-sm" style="width:110px;">
                        <option value="">'._("All Status").'</option>
                        <option value="1" '.(isset($_GET['status']) && $_GET['status'] == '1' ? 'selected' : '').'>'._("Approved").'</option>
                        <option value="2" '.(isset($_GET['status']) && $_GET['status'] == '2' ? 'selected' : '').'>'._("Pending").'</option>
                        <option value="0" '.(isset($_GET['status']) && $_GET['status'] == '0' ? 'selected' : '').'>'._("Hidden").'</option>
                    </select>
                    <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3 fw-bold shadow-sm"><i class="bi bi-funnel-fill me-1"></i> '._("Filter").'</button>
                    <a href="index.php?page=comments" class="btn btn-light btn-sm rounded-pill px-3 border shadow-sm" title="'._("Reset").'"><i class="bi bi-arrow-counterclockwise"></i></a>
                </form>',
            'body_elements' => [
                [
                    'type' => 'form',
                    'action' => '',
                    'fields' => [
                        [
                            'type' => 'table',
                            'headers' => [
                                ['content' => '<input type="checkbox" id="selectall" class="form-check-input">', 'class' => 'ps-4', 'width' => '50px'],
                                _('Comment Insight'),
                                ['content' => _('Identity'), 'class' => 'text-center'],
                                ['content' => _('Timeline'), 'class' => 'text-center'],
                                ['content' => _('Status'), 'class' => 'text-center'],
                                ['content' => _('Actions'), 'class' => 'text-end pe-4']
                            ],
                            'rows' => $rows,
                            'empty_message' => _('Your feedback history is clean.')
                        ]
                    ]
                ]
            ],
            'footer' => '
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        '.((new UiBuilder())->renderElement([
                            'type' => 'bulk_actions',
                            'options' => [
                                'publish' => _('Approve Selected'),
                                'unpublish' => _('Hide Selected'),
                                'delete' => _('Purge Selected')
                            ],
                            'button_label' => _('Process')
                        ], true)).'
                    </div>
                    <div>'.$data['paging'].'</div>
                </div>'
        ]
    ]
];

// ── RENDER ────────────────────────────────────────────────────────
echo '<div class="col-md-12">';
echo Hooks::run('admin_page_notif_action', $data);
echo '</div>';

$builder = new UiBuilder($schema);
$builder->render();
?>

<script>
    $(document).ready(function() {
        $('#selectall').click(function() { $('.check').prop('checked', this.checked); });
        $('.check').click(function() {
            if (!this.checked) $('#selectall').prop('checked', false);
            if ($('.check:checked').length == $('.check').length && $('.check').length > 0) $('#selectall').prop('checked', true);
        });
    });
</script>
