<?php
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * 
 * @since 2.0.0
 * @version 2.4.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

// ── PREPARE DATA ──────────────────────────────────────────────────
$headers = [['content' => _('Authorized Operation'), 'class' => 'ps-4 py-3', 'width' => '300px']];
$groupList = [];
foreach ($data['groups'] as $gid => $gname) {
    if ($gid == 0)
        continue; // Skip admin
    $groupList[$gid] = $gname;
    $headers[] = ['content' => _($gname), 'class' => 'text-center'];
}

$rows = [];
foreach ($data['permissions'] as $key => $p) {
    $row = [
        [
            'content' => "
            <div class='fw-bold text-dark'>{$p['label']}</div>
            <div class='extra-small text-muted font-monospace opacity-75'>{$key}</div>",
            'class' => 'ps-4 py-3'
        ]
    ];
    foreach ($groupList as $gid => $gname) {
        $status = Acl::checkGroup($key, $gid);
        $row[] = [
            'content' => "
            <div class='form-check form-switch d-inline-block'>
                <input type='hidden' name='perm[{$gid}][{$key}]' value='0'>
                <input class='form-check-input permission-switch' type='checkbox' 
                       name='perm[{$gid}][{$key}]' value='1' 
                       " . ($status ? 'checked' : '') . ">
            </div>",
            'class' => 'text-center'
        ];
    }
    $rows[] = $row;
}

// ── DEFINE UI SCHEMA ──────────────────────────────────────────────
$schema = [
    'header' => [
        'title' => _('Access Control Matrix'),
        'subtitle' => _('Define protocol access and operational capabilities for each user group.'),
        'icon' => 'bi bi-shield-lock',
        'button' => [
            'url' => '#',
            'label' => _('Apply Protocols'),
            'icon' => 'bi bi-shield-check',
            'class' => 'btn btn-primary rounded-pill px-4 shadow-sm fw-bold',
            'attr' => 'onclick="document.getElementById(\'aclForm\').submit()"'
        ],
    ],
    'content' => [
        [
            'type' => 'card',
            'no_padding' => true,
            'title' => _('Permission Registry'),
            'subtitle' => _('Toggle permissions per group. Administrator (0) bypasses all restrictions.'),
            'icon' => 'bi bi-shield-lock',
            'body_elements' => [
                [
                    'type' => 'form',
                    'action' => 'index.php?page=permissions',
                    'attr' => 'id="aclForm"',
                    'fields' => [
                        [
                            'type' => 'table',
                            'headers' => $headers,
                            'rows' => $rows
                        ]
                    ]
                ]
            ],
            'footer' => '
                <div class="alert alert-info border-0 bg-info bg-opacity-10 extra-small d-flex align-items-center mb-0 rounded-3 text-start mx-4 my-2">
                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                    <div>
                        <strong>' . _("Notice:") . '</strong> ' . _("Administrative accounts (Administrator) have hard-coded absolute access and cannot be restricted through this interface to prevent system lockouts.") . '
                    </div>
                </div>
                <input type="hidden" name="token" value="' . TOKEN . '" form="aclForm">
                <input type="hidden" name="save_acl" value="1" form="aclForm">'
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
    .permission-switch {
        cursor: pointer;
        width: 2.5em !important;
        height: 1.25em !important;
    }

    .permission-switch:checked {
        background-color: #3b82f6 !important;
        border-color: #3b82f6 !important;
    }

    .permission-switch:focus {
        box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25) !important;
    }
</style>