<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');

if (isset($_POST['change_gxeditor'])) {
    if (Token::validate($_POST['token'])) {
        Options::update('gxeditor_full_blocks', json_encode($_POST['full_blocks'] ?? []));
        Options::update('gxeditor_mini_blocks', json_encode($_POST['mini_blocks'] ?? []));
        Options::update('gxeditor_height', $_POST['gxeditor_height'] ?? '300');
        Options::update('gxeditor_style', $_POST['gxeditor_style'] ?? 'block');
        $data['alertSuccess'][] = _('GxEditor Settings Updated');
    } else {
        $data['alertDanger'][] = _('Invalid Token');
    }
}

$all_blocks = [
    'paragraph' => _('Paragraph'),
    'h1' => _('Heading 1'),
    'h2' => _('Heading 2'),
    'h3' => _('Heading 3'),
    'quote' => _('Blockquote'),
    'code' => _('Code Block'),
    'ul' => _('Bullet List'),
    'ol' => _('Numbered List'),
    'image' => _('Image'),
    'button' => _('Button'),
    'grid2' => _('2 Columns Grid'),
    'grid2x2' => _('2x2 Grid'),
    'card' => _('Card Container'),
    'icon' => _('Bootstrap Icon'),
    'divider' => _('Horizontal Divider'),
    'single_post' => _('Single Post Picker'),
    'toc' => _('Table of Contents'),
    'icon_list' => _('Icon List'),
    'table' => _('Data Table'),
    'recent_posts' => _('Recent Posts Widget'),
    'random_posts' => _('Random Posts Widget')
];

$post_blocks = json_decode(Options::v('gxeditor_full_blocks') ?? Options::v('gxeditor_post_blocks') ?? '', true) ?: array_keys($all_blocks);
$comment_blocks = json_decode(Options::v('gxeditor_mini_blocks') ?? Options::v('gxeditor_comment_blocks') ?? '', true) ?: ['paragraph', 'quote', 'code', 'ul', 'ol'];

$schema = [
    'header' => [
        'title' => _('GxEditor Settings'),
        'subtitle' => _('Configure the block-based editor experience and tool availability.'),
        'icon' => 'bi bi-pencil-square',
        'button' => [
            'type' => 'button', 'name' => 'change_gxeditor', 'label' => _('Save Configuration'), 'icon' => 'bi bi-check-circle',
            'class' => 'btn btn-primary rounded-pill px-4 shadow-sm fw-bold', 'attr' => 'value="Change"'
        ],
    ],
    'card_wrapper' => true,
    'content' => [
        ['type' => 'row', 'items' => [
            ['width' => 6, 'content' => [
                ['type' => 'card', 'title' => _('Full Editor Configuration'), 'icon' => 'bi bi-layout-text-sidebar-reverse', 'body_elements' => [
                    ['type' => 'heading', 'text' => _('Standard Blocks (Full)'), 'subtitle' => _('Define which blocks are available for primary content types like Posts and Pages.')],
                    ['type' => 'raw', 'html' => '<div class="row g-2 mb-4">'],
                    ['type' => 'raw', 'html' => (function() use ($all_blocks, $post_blocks) {
                        $html = '';
                        foreach ($all_blocks as $key => $label) {
                            $checked = in_array($key, $post_blocks) ? 'checked' : '';
                            $html .= '<div class="col-md-6"><div class="p-3 border rounded-4 bg-light transition-all hover-shadow-sm">';
                            $html .= '<div class="form-check form-switch mb-0">';
                            $html .= "<input class=\"form-check-input\" type=\"checkbox\" name=\"full_blocks[]\" value=\"{$key}\" id=\"full_{$key}\" {$checked}>";
                            $html .= "<label class=\"form-check-label fw-bold small\" for=\"full_{$key}\">{$label}</label>";
                            $html .= '</div></div></div>';
                        }
                        return $html;
                    })()],
                    ['type' => 'raw', 'html' => '</div>'],
                ]]
            ]],
            ['width' => 6, 'content' => [
                ['type' => 'card', 'title' => _('Mini Editor Configuration'), 'icon' => 'bi bi-chat-dots', 'body_elements' => [
                    ['type' => 'heading', 'text' => _('Simplified Blocks (Mini)'), 'subtitle' => _('Define restricted blocks for comments or other minor input areas.')],
                    ['type' => 'raw', 'html' => '<div class="row g-2 mb-4">'],
                    ['type' => 'raw', 'html' => (function() use ($all_blocks, $comment_blocks) {
                        $html = '';
                        foreach ($all_blocks as $key => $label) {
                            $checked = in_array($key, $comment_blocks) ? 'checked' : '';
                            $html .= '<div class="col-md-6"><div class="p-3 border rounded-4 bg-light transition-all hover-shadow-sm">';
                            $html .= '<div class="form-check form-switch mb-0">';
                            $html .= "<input class=\"form-check-input\" type=\"checkbox\" name=\"mini_blocks[]\" value=\"{$key}\" id=\"mini_{$key}\" {$checked}>";
                            $html .= "<label class=\"form-check-label fw-bold small\" for=\"mini_{$key}\">{$label}</label>";
                            $html .= '</div></div></div>';
                        }
                        return $html;
                    })()],
                    ['type' => 'raw', 'html' => '</div>'],
                ]]
            ]]
        ]],
        ['type' => 'row', 'items' => [
            ['width' => 4, 'content' => [
                ['type' => 'card', 'title' => _('Editor Dimensions'), 'icon' => 'bi bi-arrows-fullscreen', 'body_elements' => [
                    ['type' => 'input', 'label' => _('Default Editor Height (px)'), 'name' => 'gxeditor_height', 'value' => Options::v('gxeditor_height') ?: '300', 'input_type' => 'number'],
                ]]
            ]],
            ['width' => 4, 'content' => [
                ['type' => 'card', 'title' => _('UI Customization'), 'icon' => 'bi bi-palette', 'body_elements' => [
                    ['type' => 'select', 'label' => _('Editor Interaction Model'), 'name' => 'gxeditor_style', 'selected' => Options::v('gxeditor_style') ?: 'block', 'options' => [
                        'block' => _('Modern Block (Gutenberg Style)'),
                        'classic' => _('Standard Classic (Floating Sidebar / Top Toolbar)')
                    ]],
                ]]
            ]],
            ['width' => 4, 'content' => [
                ['type' => 'alert', 'style' => 'primary', 'content' => '
                    <div class="d-flex align-items-center">
                        <i class="bi bi-info-circle-fill fs-3 me-3 opacity-50"></i>
                        <div>
                            <h6 class="fw-black mb-1">Architecture</h6>
                            <p class="extra-small mb-0 opacity-75 fw-bold text-uppercase">Choice here influences how blocks are grouped and rendered in the admin area.</p>
                        </div>
                    </div>
                ']
            ]]
        ]]
    ]
];

echo '<div class="col-md-12">';
echo Hooks::run('admin_page_notif_action', $data);
echo '</div>';

echo '<form action="" method="POST">';
$ui = new UiBuilder($schema);
$ui->render();
echo '<input type="hidden" name="token" value="'.TOKEN.'">';
echo '</form>';
