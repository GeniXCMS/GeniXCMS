<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 */

// ── PREPARE DATA ──────────────────────────────────────────────────
$username = Session::val('username');
$group = Session::val('group');

$rows = [];
if ($data['num'] > 0) {
    foreach ($data['posts'] as $p) {
        $pObj = (object)$p;
        $accessEdit = $group <= 2 ? 1 : ($pObj->author == $username ? 1 : 0);
        $accessDelete = $group < 2 ? 1 : 0;
        
        $status = ($pObj->status == '0') ? 'warning' : 'success';
        $statusLabel = ($pObj->status == '0') ? _("Draft") : _("Live");
        
        // Interaction Logic
        $views = number_format($pObj->views ?? 0);
        
        // Thumbnail Logic (Optional for pages, but good for visual distinction)
        $post_image = Posts::getPostImage($pObj->id);
        $thumb = ($post_image != "") ? $post_image : Posts::getImage(Typo::Xclean($pObj->content), 1);
        $thumbUrl = ($thumb != '') ? Url::thumb($thumb, 'square', 100) : Site::$url.'assets/images/noimage.png';

        $actions = '<div class="btn-group gap-1">';
        $actions .= '<a href="'.Url::page($pObj->id).'" target="_blank" class="btn btn-light btn-sm rounded-circle border" title="'._("Preview").'"><i class="bi bi-eye text-primary"></i></a>';
        if ($accessEdit) {
            $actions .= '<a href="index.php?page=pages&act=edit&id='.$pObj->id.'&token='.TOKEN.'" class="btn btn-light btn-sm rounded-circle border" title="'._("Edit").'"><i class="bi bi-pencil-square text-success"></i></a>';
        }
        if ($accessDelete) {
            $actions .= '<a href="index.php?page=pages&act=del&id='.$pObj->id.'&token='.TOKEN.'" class="btn btn-light btn-sm rounded-circle border" onclick="return confirm(\''._("Are you sure?").'\');" title="'._("Delete").'"><i class="bi bi-trash text-danger"></i></a>';
        }
        $actions .= '</div>';

        $rows[] = [
            ['content' => "
                <div class='d-flex align-items-center ps-4 py-2'>
                    <div class='me-3 position-relative'>
                        <img src='{$thumbUrl}' class='rounded-3 shadow-sm border' width='50' height='50' style='object-fit: cover;'>
                        <span class='position-absolute top-0 start-100 translate-middle badge rounded-pill bg-white border text-dark extra-small' style='font-size: 0.6rem;'>#{$pObj->id}</span>
                    </div>
                    <div>
                        <a href='index.php?page=pages&act=edit&id={$pObj->id}&token=".TOKEN."' class='fw-bold text-dark text-decoration-none d-block mb-1 ls-n1' style='font-size: 0.95rem;'>".((strlen($pObj->title) > 60) ? substr($pObj->title, 0, 57).'...' : $pObj->title)."</a>
                        <span class='badge bg-{$status} bg-opacity-10 text-{$status} border border-{$status} border-opacity-25 rounded-pill px-2 fw-bold text-uppercase ls-1' style='font-size: 0.65rem;'>{$statusLabel}</span>
                    </div>
                </div>", 'class' => 'p-0'],
            ['content' => "
                <div class='d-flex align-items-center justify-content-center'>
                    <img src='".Image::getGravatar(User::email($pObj->author), 40)."' class='rounded-circle me-2 border p-1 bg-white' width='32'>
                    <div class='text-start'>
                        <div class='small fw-bold text-dark mb-0'>{$pObj->author}</div>
                        <div class='text-muted extra-small'>"._("Curator")."</div>
                    </div>
                </div>", 'class' => 'text-center'],
            ['content' => "
                <div class='d-flex flex-column align-items-center justify-content-center opacity-75'>
                    <div class='d-flex align-items-center gap-2 mb-1'>
                        <i class='bi bi-activity text-primary'></i>
                        <span class='small fw-bold text-dark'>{$views}</span>
                    </div>
                    <div class='extra-small text-muted text-uppercase fw-bold ls-1'>"._("Visibility")."</div>
                </div>", 'class' => 'text-center'],
            ['content' => "
                <div class='text-center'>
                    <div class='small fw-bold text-dark mb-0'>".Date::format($pObj->date, 'd M Y')."</div>
                    <div class='text-muted extra-small'>".Date::format($pObj->date, 'H:i A')."</div>
                </div>", 'class' => 'text-center'],
            ['content' => $actions, 'class' => 'text-center'],
            ['content' => "<div class='text-center pe-4'><input type='checkbox' name='post_id[]' value='{$pObj->id}' class='check form-check-input shadow-none border'></div>", 'class' => 'p-0']
        ];
    }
}

// Stats Data
$statsItems = [
    ['label' => _('Information Library'), 'value' => (string)Stats::totalPost('page'), 'icon' => 'bi bi-journal-album', 'color' => 'primary'],
    ['label' => _('Active Assets'), 'value' => (string)Stats::activePost('page'), 'icon' => 'bi bi-broadcast-pin', 'color' => 'success'],
    ['label' => _('Archived / Pending'), 'value' => (string)Stats::inactivePost('page'), 'icon' => 'bi bi-archive', 'color' => 'warning']
];

// ── DEFINE UI SCHEMA ──────────────────────────────────────────────
$schema = [
    'header' => [
        'title' => _('Information Registry'),
        'subtitle' => _('Architect site structure and permanent informational nodes with real-time visibility metrics.'),
        'icon' => 'bi bi-stack',
        'button' => [
            'url' => 'index.php?page=pages&act=add&token=' . TOKEN,
            'label' => _('New Page'),
            'icon' => 'bi bi-plus-lg',
            'class' => 'btn btn-primary rounded-pill px-4 shadow-sm fw-bold'
        ],
    ],
    'content' => [
        ['type' => 'stat_cards', 'size' => 'small', 'items' => $statsItems],
        [
            'type' => 'card',
            'title' => _('Navigation Nodes'),
            'icon' => 'bi bi-geo-fill',
            'no_padding' => true,
            'header_action' => '
                <form action="index.php?page=pages" method="get" class="d-flex gap-2 flex-wrap justify-content-end align-items-center">
                    <input type="hidden" name="page" value="pages">
                    <div class="input-group input-group-sm w-auto shadow-sm rounded-pill overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-3"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control border-0 ps-1 bg-white" placeholder="'._("Locate page...").'" style="width:160px;" value="'.($_GET['q'] ?? '').'">
                    </div>
                    <div class="d-flex gap-1 align-items-center bg-white border rounded-pill px-2 shadow-sm">
                        <i class="bi bi-calendar-event text-muted ms-1" style="font-size:0.75rem;"></i>
                        <input type="date" name="from" class="form-control form-control-sm border-0 bg-transparent p-1" style="font-size:0.75rem; width:110px;" value="'.($_GET['from'] ?? '').'" title="'._("From Date").'">
                        <span class="text-muted small">-</span>
                        <input type="date" name="to" class="form-control form-control-sm border-0 bg-transparent p-1" style="font-size:0.75rem; width:110px;" value="'.($_GET['to'] ?? '').'" title="'._("To Date").'">
                    </div>
                    <select name="status" class="form-select form-select-sm rounded-pill px-3 shadow-none border bg-white shadow-sm" style="width:110px;">
                        <option value="">'._("All Status").'</option>
                        <option value="1" '.(isset($_GET['status']) && $_GET['status'] == '1' ? 'selected' : '').'>'._("Live").'</option>
                        <option value="0" '.(isset($_GET['status']) && $_GET['status'] == '0' ? 'selected' : '').'>'._("Draft").'</option>
                    </select>
                    <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3 fw-bold shadow-sm"><i class="bi bi-funnel-fill me-1"></i> '._("Filter").'</button>
                    <a href="index.php?page=pages" class="btn btn-light btn-sm rounded-pill px-3 border shadow-sm" title="'._("Reset").'"><i class="bi bi-arrow-counterclockwise"></i></a>
                </form>',
            'body_elements' => [
                [
                    'type' => 'form',
                    'action' => '',
                    'attr' => 'id="pages-bulk-form"',
                    'fields' => [
                        [
                            'type' => 'table',
                            'headers' => [
                                ['content' => _('Architecture Details'), 'class' => 'ps-4 py-3'],
                                ['content' => _('Accountability'), 'class' => 'text-center'],
                                ['content' => _('Engagement'), 'class' => 'text-center'],
                                ['content' => _('Timeline'), 'class' => 'text-center'],
                                ['content' => _('Management'), 'class' => 'text-center'],
                                ['content' => '<div class="text-center pe-4"><input type="checkbox" id="selectall" class="form-check-input shadow-none border"></div>', 'class' => 'p-0', 'width' => '50px']
                            ],
                            'rows' => $rows,
                            'empty_message' => _('Your information library is currently empty.')
                        ],
                        ['type' => 'raw', 'html' => '<input type="hidden" name="token" value="'.TOKEN.'">']
                    ]
                ]
            ],
            'footer' => '
                <div class="d-flex justify-content-between align-items-center w-100 p-2">
                    <div class="bulk-action-wrapper">
                        '.((new UiBuilder())->renderElement([
                            'type' => 'bulk_actions',
                            'button_label' => _('Apply Governance'),
                            'options' => [
                                'publish' => _('Authorize to Live'),
                                'unpublish' => _('Revoke to Draft'),
                                'delete' => _('Permanent Deletion')
                            ],
                            'form' => 'pages-bulk-form'
                        ], true)).'
                    </div>
                    <div class="pagination-wrapper">'.$data['paging'].'</div>
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

<style>
    .ls-1 { letter-spacing: 0.5px; }
    .ls-n1 { letter-spacing: -0.5px; }
    .extra-small { font-size: 0.65rem !important; }
    .pagination-wrapper .pagination { margin-bottom: 0; gap: 5px; }
    .pagination-wrapper .page-link { border-radius: 50% !important; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border: 0; background: #f8f9fa; color: #6c757d; font-weight: bold; font-size: 0.85rem; }
    .pagination-wrapper .page-item.active .page-link { background: var(--gx-primary); color: #fff; box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2); }
</style>

<script>
    $(document).ready(function() {
        $('#selectall').click(function() { $('.check').prop('checked', this.checked); });
        $('.check').click(function() {
            if (!this.checked) $('#selectall').prop('checked', false);
            if ($('.check:checked').length == $('.check').length && $('.check').length > 0) $('#selectall').prop('checked', true);
        });
    });
</script>
