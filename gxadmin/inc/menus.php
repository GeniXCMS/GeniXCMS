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


// ── PREPARE DATA ──────────────────────────────────────────────────
$menuItems = [];
if (isset($data['menus']) && $data['menus'] != '') {
    $menus = json_decode(Typo::Xclean($data['menus']), true);
    $first = true;
    foreach ($menus as $k => $m) {
        // Inner Content for each menu (Structure + Add Form)
        ob_start();
        ?>
        <ul class="nav nav-pills custom-pills mb-4" id="<?= $k; ?>-tab">
            <li class="nav-item"><a class="nav-link active rounded-pill px-4 me-2 bg-white border text-secondary"
                    data-bs-toggle="pill" href="#<?= $k; ?>-menuitem"><?= _("Structure"); ?></a></li>
            <li class="nav-item"><a class="nav-link rounded-pill px-4 bg-white border text-secondary" data-bs-toggle="pill"
                    href="#<?= $k; ?>-additem"><?= _("Add Link"); ?></a></li>
        </ul>
        <div class="tab-content" id="<?= $k; ?>-tabContent">
            <div class="tab-pane fade show active" id="<?= $k; ?>-menuitem">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white"><?php echo Menus::getMenuAdmin($k, ''); ?></div>
            </div>
            <div class="tab-pane fade" id="<?= $k; ?>-additem">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <?php
                    $data['parent'] = Menus::isHadParent('', $k);
                    $data['menuid'] = $k;
                    System::inc('menus_form', $data);
                    ?>
                </div>
            </div>
        </div>
        <?php
        $innerHtml = ob_get_clean();

        $menuItems[] = [
            'active' => $first,
            'header_html' => "
                <div class='d-flex align-items-center w-100'>
                    <div class='bg-primary bg-opacity-10 p-2 rounded-3 text-primary me-3'><i class='bi bi-list fs-5'></i></div>
                    <div class='flex-grow-1 text-start'>
                        <div class='fw-bold text-dark h5 mb-0'>{$m['name']}</div>
                        <div class='extra-small text-muted fw-bold text-uppercase tracking-wider'>ID: {$k}</div>
                    </div>
                    <div class='me-3'>
                        <a href='index.php?page=menus&act=remove&menuid={$k}&token=" . TOKEN . "' class='btn btn-light btn-sm rounded-pill px-3 border' onclick=\"return confirm('" . _("Remove entire menu?") . "');\">
                            <i class='bi bi-trash text-danger me-1'></i> <span class='extra-small fw-bold text-uppercase'>Delete</span>
                        </a>
                    </div>
                </div>",
            'body_elements' => [['type' => 'raw', 'html' => $innerHtml]]
        ];
        $first = false;
    }
}

// ── DEFINE UI SCHEMA ──────────────────────────────────────────────
$schema = [
    'header' => [
        'title' => _('Navigation Architect'),
        'subtitle' => _('Define and organize your site\'s structural navigation menus.'),
        'icon' => 'bi bi-node-plus',
        'button' => [
            'url' => '#',
            'label' => _('New Menu'),
            'icon' => 'bi bi-node-plus-fill',
            'class' => 'btn btn-primary rounded-pill px-4 shadow-sm fw-bold',
            'attr' => 'data-bs-toggle="modal" data-bs-target="#myModal"'
        ],
    ],
    'content' => [
        [
            'type' => 'card',
            'no_padding' => true,
            'body_elements' => [
                ['type' => 'accordion', 'id' => 'menuArchitect', 'items' => $menuItems]
            ]
        ],
        // Modal
        [
            'type' => 'modal',
            'id' => 'myModal',
            'header' => _("Architect New Menu"),
            'size' => 'md',
            'body_elements' => [
                [
                    'type' => 'form',
                    'action' => 'index.php?page=menus',
                    'fields' => [
                        ['type' => 'input', 'name' => 'id', 'label' => _("Menu Registry ID"), 'placeholder' => 'e.g. main-nav', 'required' => true],
                        ['type' => 'input', 'name' => 'name', 'label' => _("Friendly Name"), 'required' => true],
                        ['type' => 'input', 'name' => 'class', 'label' => _("Custom CSS Class"), 'placeholder' => 'e.g. navbar-nav'],
                        ['type' => 'raw', 'html' => '<input type="hidden" name="token" value="' . TOKEN . '">'],
                        ['type' => 'button', 'name' => 'submit', 'label' => _("Initialize Menu"), 'class' => 'btn btn-primary rounded-pill px-5 fw-bold w-100 mt-3']
                    ]
                ]
            ]
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
    .custom-pills .nav-link.active {
        background-color: var(--gx-primary) !important;
        color: #fff !important;
        border-color: var(--gx-primary) !important;
        box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2);
    }

    .accordion-button:not(.collapsed) {
        background-color: #fff !important;
    }
</style>