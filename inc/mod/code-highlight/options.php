<?php
/**
 * GeniXCMS - Content Management Systemhouse.
 *
 * PHP Based Content Management System and Framework
 */
?>

<?php
// ── PREPARE SCHEMA ────────────────────────────────────────────────
$schema = [
    'header' => [
        'title' => _('Code Highlighting'),
        'subtitle' => _('Automated syntax highlighting for technical documentation and source snippets.'),
        'icon' => 'bi bi-terminal-fill',
        'header_action' => [
            ['type' => 'raw', 'html' => '
                <div class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-4 py-2 border border-primary border-opacity-25 fw-black text-uppercase tracking-widest fs-9 ms-3 shadow-none mt-1">
                    <i class="bi bi-cpu-fill me-1"></i> Engine: highlight.js v9.6.0
                </div>']
        ]
    ],
    'card_wrapper' => false,
    'content' => [
        ['type' => 'row', 'items' => [
            ['width' => 8, 'content' => [
                ['type' => 'card', 'full_height' => true, 'body_elements' => [
                    ['type' => 'heading', 'text' => _('Operational Profile'), 'icon' => 'bi bi-activity', 'subtitle' => _('Zero-config integration status for the highlighting engine.')],
                    ['type' => 'raw', 'html' => '
                        <div class="bg-light rounded-4 p-4 mb-4 border-start border-4 border-primary shadow-none d-flex align-items-center gap-4">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-3 d-flex align-items-center justify-content-center shadow-none" style="width:60px; height:60px;">
                                <i class="bi bi-magic text-primary fs-3"></i>
                            </div>
                            <div>
                                <h6 class="fw-black text-dark text-uppercase tracking-widest fs-8 mb-1">'._("Automated Transformation").'</h6>
                                <p class="text-muted small fw-bold mb-0">
                                    '._("Standard <pre> tags are instantly detected and injected with rich Monokai aesthetics.").'
                                </p>
                            </div>
                        </div>'],
                    ['type' => 'heading', 'text' => _('Implementation Guide'), 'icon' => 'bi bi-journal-code', 'subtitle' => _('Wrap your code snippets in standard tags to activate.')],
                    ['type' => 'raw', 'html' => '
                        <div class="bg-dark rounded-4 p-4 position-relative overflow-hidden mb-4 shadow-sm border border-secondary border-opacity-25">
                            <div class="fw-black text-white opacity-25 position-absolute top-0 end-0 m-3 extra-small tracking-widest">SNIPPET EXAMPLE</div>
                            <pre class="m-0 fs-8 fw-bold font-monospace"><code class="text-info">&lt;pre&gt;</code>
    <code class="text-light">function helloWorld() {
        console.log("Hello GeniXCMS!");
    }</code>
<code class="text-info">&lt;/pre&gt;</code></pre>
                        </div>'],
                    ['type' => 'alert', 'style' => 'info', 'content' => '
                        <h6 class="fw-black text-info text-uppercase fs-9 mb-2 tracking-widest"><i class="bi bi-stars me-2"></i> Technical Capability</h6>
                        <p class="extra-small mb-0 lh-base fw-bold">'._("Supports over 180 languages with consistent cross-browser styling and automated language detection logic.").'</p>']
                ]]
            ]],
            ['width' => 4, 'content' => [
                ['type' => 'card', 'body_elements' => [
                    ['type' => 'raw', 'html' => '
                        <div class="text-center py-2">
                            <div class="bg-primary bg-opacity-10 rounded-4 d-inline-flex align-items-center justify-content-center mb-3 transition-hover shadow-none" style="width: 60px; height: 60px;">
                                <i class="bi bi-code-square fs-2 text-primary"></i>
                            </div>
                            <h6 class="fw-black text-dark text-uppercase tracking-widest fs-8 mb-1">'._("Code Highlight").'</h6>
                            <div class="extra-small text-muted fw-bold mb-3 opacity-75">'._("v9.6.0 Protocol").'</div>
                            <div class="d-grid gap-2">
                                <a href="https://highlightjs.org" target="_blank" class="btn btn-light rounded-pill btn-sm fw-black text-uppercase tracking-wider border py-2 fs-9 shadow-none">
                                    <i class="bi bi-diagram-3-fill me-2 text-primary"></i> '._("Docs").'
                                </a>
                            </div>
                        </div>']
                ], 'footer' => '
                    <div class="d-flex align-items-center justify-content-between extra-small fw-black text-muted text-uppercase tracking-widest py-0">
                        <span>'._("Status").'</span>
                        <span class="text-primary">'._("READY")."</span>
                    </div>"],
                ['type' => 'card', 'title' => _('Dev Tip'), 'icon' => 'bi bi-incognito', 'body_elements' => [
                    ['type' => 'raw', 'html' => '
                        <div class="bg-dark rounded-4 p-3 border border-secondary border-opacity-25 d-flex gap-3 align-items-center shadow-sm mt-1">
                            <i class="bi bi-terminal-dash text-info fs-5"></i>
                            <div class="extra-small text-white-50 fw-bold lh-sm">
                                '._("Target:").' <code>&lt;pre class="php"&gt;</code>
                            </div>
                        </div>']
                ]]
            ]]
        ]]
    ]
];

// ── RENDER ────────────────────────────────────────────────────────
echo '<div class="col-md-12">';
echo Hooks::run('admin_page_notif_action', $data ?? []);
echo '</div>';

$builder = new UiBuilder($schema);
$builder->render();
?>

<style>
    .transition-hover { transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .transition-hover:hover { transform: scale(1.05) rotate(5deg); }
    .fs-9 { font-size: 0.65rem !important; }
</style>
