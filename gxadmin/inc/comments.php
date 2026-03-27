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
        ['type' => 'stat_cards', 'items' => [
            ['label' => _('Total Feed'), 'value' => (string)Stats::totalComments(), 'icon' => 'bi bi-chat-dots', 'color' => 'primary'],
            ['label' => _('Active'), 'value' => (string)Stats::activeComments(), 'icon' => 'bi bi-patch-check', 'color' => 'success'],
            ['label' => _('Pending'), 'value' => (string)Stats::pendingComments(), 'icon' => 'bi bi-hourglass-split', 'color' => 'warning'],
            ['label' => _('Spam/Blocked'), 'value' => (string)Stats::inactiveComments(), 'icon' => 'bi bi-shield-exclamation', 'color' => 'danger']
        ]],
        [
            'type' => 'card',
            'no_padding' => true,
            'header_action' => '
                <form action="index.php?page=comments" method="get" class="d-flex gap-2">
                    <input type="hidden" name="page" value="comments">
                    <input type="hidden" name="token" value="'.TOKEN.'">
                    <input type="text" name="q" class="form-control form-control-sm rounded-pill px-3 shadow-none border bg-light" placeholder="'._("Keyword...").'" style="width:200px;">
                    <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3 fw-bold"><i class="bi bi-funnel"></i></button>
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