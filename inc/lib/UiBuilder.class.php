<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * UiBuilder Class
 *
 * A core class to build dynamic backend web elements based on arrays.
 * Operates similarly to OptionsBuilder but is designed for generic backend
 * module pages (dashboards, tables, forms, stat cards) rather than just options.
 *
 * @since 2.0.0
 */
class UiBuilder
{
    private array $schema;
    private string $activeTab;

    public function __construct(array $schema = [])
    {
        $this->schema = $schema;
        $this->activeTab = isset($_GET['tab']) ? Typo::cleanX($_GET['tab']) : ($schema['default_tab'] ?? '');
    }

    public function renderHeader(): void
    {
        if (isset($this->schema['header'])) {
            $h = $this->schema['header'];
            $icon = (isset($h['icon']) && $h['icon'] != "") ? "<i class=\"{$h['icon']} me-2\"></i>" : "";
            $title = $h['title'] ?? 'Module';
            $subtitle = $h['subtitle'] ?? '';

            echo '<div class="row align-items-center mb-4 sticky-top bg-white py-3 shadow-sm border-bottom gx-module-header transition-all" style="top: 50px; z-index: 1020; margin-left: -20px; margin-right: -20px; padding-left: 20px; padding-right: 20px;">';
            echo '    <div class="col-md-6">';
            echo "        <h3 class=\"fw-bold text-dark mb-0 module-title transition-all\">{$icon}{$title}</h3>";
            if ($subtitle) {
                echo "        <p class=\"text-muted small mb-0 module-subtitle transition-all\">{$subtitle}</p>";
            }
            echo '    </div>';
            echo '    <div class="col-md-6 text-md-end">';
            
            // Handle Multiple Buttons or Single Button
            $buttons = [];
            if (isset($h['buttons'])) {
                $buttons = $h['buttons'];
            } elseif (isset($h['button'])) {
                $buttons[] = $h['button'];
            }

            foreach ($buttons as $btn) {
                $btnUrl = $btn['url'] ?? '#';
                $btnLabel = $btn['label'] ?? 'Action';
                $btnIcon = (isset($btn['icon']) && $btn['icon'] != "") ? "<i class=\"{$btn['icon']} me-1\"></i>" : "";
                $btnClass = $btn['class'] ?? 'btn btn-primary rounded-pill px-4 shadow-sm';
                $btnAttr = $btn['attr'] ?? '';
                $btnType = $btn['type'] ?? 'link';

                if ($btnType === 'button') {
                    echo "<button type=\"submit\" name=\"submit\" class=\"{$btnClass} module-action-btn transition-all\" {$btnAttr}>{$btnIcon}{$btnLabel}</button> ";
                } else {
                    echo "<a href=\"{$btnUrl}\" class=\"{$btnClass} module-action-btn transition-all\" {$btnAttr}>{$btnIcon}{$btnLabel}</a> ";
                }
            }
            echo '    </div>';
            echo '</div>';

            echo '<style>
                .gx-module-header.shrunk { padding-top: 0.5rem !important; padding-bottom: 0.5rem !important; }
                .gx-module-header.shrunk .module-title { font-size: 1.15rem !important; }
                .gx-module-header.shrunk .module-subtitle { font-size: 0.65rem !important; display: none; }
                .gx-module-header.shrunk .module-action-btn { font-size: 0.75rem !important; padding: 0.4rem 1.25rem !important; }
                .transition-all { transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
            </style>';
            echo '<script>
                $(window).on("scroll.gx-header", function() {
                    var st = $(window).scrollTop();
                    var header = $(".gx-module-header");
                    if (st > 120) {
                        if (!header.hasClass("shrunk")) header.addClass("shrunk");
                    } else if (st < 40) {
                        if (header.hasClass("shrunk")) header.removeClass("shrunk");
                    }
                });
            </script>';
        }
    }

    public function render(): void
    {
        echo '<div class="container-fluid py-4">';

        // Render Header (Matches GeniXCMS Admin Style)
        $this->renderHeader();

        // Render Tabs Navigation as Bootstrap 5 Pills
        if (isset($this->schema['tabs']) && count($this->schema['tabs']) > 0) {
            $tabMode = $this->schema['tab_mode'] ?? 'link';
            $tabStyle = $this->schema['tab_style'] ?? 'pills';
            
            $tabClass = match($tabStyle) {
                'tabs' => 'nav-tabs border-0 px-4 pt-4',
                'modern' => 'nav-tabs border-0 border-bottom mb-5 modern-tabs',
                default => 'nav-pills gap-2 mb-4'
            };
            
            $linkClass = match($tabStyle) {
                'tabs' => 'rounded-top-4 px-4 py-3 bg-white border-bottom-0',
                'modern' => 'border-0 border-bottom border-3 border-transparent bg-transparent px-4 py-3 text-muted',
                default => 'rounded-pill px-4 mb-4'
            };

            if ($tabStyle === 'modern') {
                echo '<style>
                    .modern-tabs .nav-link { transition: all 0.2s ease; border-bottom: 3px solid transparent !important; }
                    .modern-tabs .nav-link.active { border-color: var(--bs-primary) !important; color: var(--bs-primary) !important; font-weight: 900 !important; }
                    .modern-tabs .nav-link:hover:not(.active) { border-color: rgba(0,0,0,0.1) !important; color: var(--bs-dark) !important; }
                </style>';
            }

            if (isset($this->schema['card_wrapper']) && $this->schema['card_wrapper']) {
                echo '<div class="card border-0 shadow-sm rounded-5 overflow-hidden mb-5">';
                echo '    <div class="card-header bg-light bg-opacity-50 border-0 p-0">';
            }

            echo "<ul class=\"nav {$tabClass}\" id=\"uiTab\" role=\"tablist\">";
            $first = true;
            foreach ($this->schema['tabs'] as $id => $tab) {
                if (empty($this->activeTab) && $first) {
                    $this->activeTab = $id;
                }
                
                $activeClass = ($this->activeTab === $id) ? 'active shadow-none' : 'text-secondary';
                if ($tabStyle === 'pills' && $this->activeTab === $id) $activeClass .= ' shadow-sm';
                
                $icon = isset($tab['icon']) ? "<i class=\"{$tab['icon']} me-1\"></i> " : "";
                
                if ($tabMode === 'js') {
                    $selected = ($this->activeTab === $id) ? 'true' : 'false';
                    echo "<li class=\"nav-item\" role=\"presentation\">
                            <button class=\"nav-link fw-bold {$linkClass} {$activeClass}\" 
                                    id=\"tab-{$id}\" data-bs-toggle=\"tab\" data-bs-target=\"#content-{$id}\" 
                                    type=\"button\" role=\"tab\" aria-controls=\"content-{$id}\" aria-selected=\"{$selected}\">
                                {$icon}{$tab['label']}
                            </button>
                          </li>";
                } else {
                    $url = $tab['url'] ?? "index.php?page=" . ($_GET['page'] ?? '') . (isset($_GET['mod']) ? "&mod=".$_GET['mod'] : "") . "&tab={$id}";
                    echo "<li class=\"nav-item\"><a class=\"nav-link fw-bold {$linkClass} {$activeClass}\" href=\"{$url}\">{$icon}{$tab['label']}</a></li>";
                }
                $first = false;
            }
            echo '</ul>';
            
            if (isset($this->schema['card_wrapper']) && $this->schema['card_wrapper']) {
                if ($tabStyle !== 'modern') {
                    echo '    </div>';
                    echo '    <div class="card-body p-5 pt-4">';
                } else {
                   echo '    </div>';
                   echo '    <div class="card-body p-5 pt-0">';
                }
            }

            if ($tabMode === 'js') {
                echo '<div class="tab-content" id="uiTabContent">';
                foreach ($this->schema['tabs'] as $id => $tab) {
                    $activeClass = ($this->activeTab === $id) ? 'show active' : '';
                    echo "<div class=\"tab-pane fade {$activeClass}\" id=\"content-{$id}\" role=\"tabpanel\" aria-labelledby=\"tab-{$id}\">";
                    foreach ($tab['content'] ?? [] as $element) {
                        $this->renderElement($element);
                    }
                    echo '</div>';
                }
                echo '</div>';
                
                if (isset($this->schema['card_wrapper']) && $this->schema['card_wrapper']) {
                    echo '    </div>';
                    echo '</div>';
                }
                
                echo '</div>'; // close container-fluid
                return; 
            }
        }

        // Render Active Tab Content (Fallback for Link Mode)
        if (isset($this->schema['tabs'][$this->activeTab]['content'])) {
            $content = $this->schema['tabs'][$this->activeTab]['content'];
            foreach ($content as $element) {
                $this->renderElement($element);
            }
            
            if (isset($this->schema['card_wrapper']) && $this->schema['card_wrapper']) {
                echo '    </div>';
                echo '</div>';
            }
        } else {
            // Render direct content if no tabs
            if (isset($this->schema['content'])) {
                foreach ($this->schema['content'] as $element) {
                    $this->renderElement($element);
                }
            }
        }

        echo '</div>'; // container-fluid
    }

    public function renderElement(array $el, bool $return = false): string
    {
        if ($return) {
            ob_start();
        }
        $type = $el['type'] ?? 'raw';

        switch ($type) {
            case 'row':
                echo '<div class="row g-4 mb-4">';
                foreach ($el['items'] ?? [] as $col) {
                    $w = $col['width'] ?? 12;
                    echo "<div class=\"col-lg-{$w}\">";
                    if (is_array($col['content'])) {
                        if (isset($col['content']['type'])) {
                            $this->renderElement($col['content']);
                        } else {
                            foreach ($col['content'] as $subEl) {
                                $this->renderElement($subEl);
                            }
                        }
                    } else {
                        echo $col['content'];
                    }
                    echo '</div>';
                }
                echo '</div>';
                break;

            case 'stat_cards':
                echo '<div class="row g-4 mb-4">';
                $colors = ['primary', 'success', 'warning', 'info', 'danger', 'secondary'];
                foreach ($el['items'] ?? [] as $i => $stat) {
                    $w = $stat['width'] ?? 3;
                    $color = $stat['color'] ?? $colors[$i % count($colors)];
                    $icon = $stat['icon'] ?? 'bi bi-reception-4';
                    $val = $stat['value'] ?? '0';
                    $lbl = $stat['label'] ?? '';
                    
                    echo "
                    <div class=\"col-xl-{$w} col-md-6\">
                        <div class=\"card border-0 shadow-sm rounded-5 h-100 p-2 overflow-hidden position-relative stats-card text-{$color}\">
                            <div class=\"card-body p-4\">
                                <div class=\"d-flex justify-content-between align-items-start mb-3\">
                                    <div class=\"stats-icon-bg bg-{$color} bg-opacity-10 rounded-4 d-flex align-items-center justify-content-center\" style=\"width:60px; height:60px;\">
                                        <i class=\"{$icon} fs-3 text-{$color}\"></i>
                                    </div>
                                    <div class=\"text-end\">
                                        <div class=\"extra-small fw-bold text-muted text-uppercase tracking-widest mb-1\" style=\"font-size:0.7rem; letter-spacing:0.1em;\">{$lbl}</div>
                                        <h2 class=\"fw-black m-0 mb-n1 counter-value\" style=\"font-weight:900;\">{$val}</h2>
                                    </div>
                                </div>";
                    
                    if (isset($stat['footer_link'])) {
                        $fl = $stat['footer_link'];
                        echo "<div class=\"d-flex align-items-center justify-content-between mt-3\">
                                <div class=\"extra-small text-muted fw-bold\">" . ($stat['footer_text'] ?? '') . "</div>
                                <a href=\"{$fl}\" class=\"btn btn-light btn-sm rounded-pill px-3 fs-8 fw-bold\">
                                    View All <i class=\"bi bi-chevron-right ms-1\"></i>
                                </a>
                              </div>";
                    }

                    echo "  </div>
                            <div class=\"position-absolute bottom-0 end-0\" style=\"width:100px;height:100px;background:currentColor;opacity:0.03;border-radius:50%;margin-right:-20px;margin-bottom:-20px;pointer-events:none;\"></div>
                        </div>
                    </div>";
                }
                echo '</div>';
                break;

            case 'card':
                $h100 = (isset($el['full_height']) && $el['full_height']) ? 'h-100' : '';
                echo '<div class="card border-0 shadow-sm rounded-5 overflow-hidden mb-4 ' . $h100 . (isset($el['class']) ? " " . $el['class'] : "") . '">';
                
                if (isset($el['title'])) {
                    $icon = isset($el['icon']) ? "<i class=\"{$el['icon']} me-2 text-primary\"></i> " : "";
                    $sub = isset($el['subtitle']) ? "<p class=\"extra-small text-muted mb-0\" style=\"font-size:0.75rem;\">{$el['subtitle']}</p>" : "";
                    echo "<div class=\"card-header bg-white border-0 py-4 px-4\">
                            <div class=\"d-flex justify-content-between align-items-center\">
                                <div>
                                    <h5 class=\"fw-bold text-dark m-0 d-flex align-items-center\">{$icon}{$el['title']}</h5>
                                    {$sub}
                                </div>";
                    if (isset($el['header_action'])) {
                        echo '<div>';
                        if (is_array($el['header_action'])) {
                            if (isset($el['header_action']['type'])) {
                                $this->renderElement($el['header_action']);
                            } else {
                                foreach ($el['header_action'] as $subAct) {
                                    $this->renderElement($subAct);
                                }
                            }
                        } else {
                            echo $el['header_action'];
                        }
                        echo '</div>';
                    }
                    echo "  </div>
                          </div>";
                }
                
                $paddingClass = (isset($el['no_padding']) && $el['no_padding']) ? 'p-0' : 'p-4';
                echo "<div class=\"card-body {$paddingClass}\">";
                if (isset($el['body_elements'])) {
                    foreach ($el['body_elements'] as $bel) {
                        $this->renderElement($bel);
                    }
                } elseif (isset($el['html'])) {
                    echo $el['html'];
                }
                echo "</div>";
                
                if (isset($el['footer'])) {
                    echo "<div class=\"card-footer bg-light bg-opacity-50 border-0 py-3 text-center border-top\">{$el['footer']}</div>";
                }
                
                echo "</div>";
                break;

            case 'tabs_nav':
                $tabStyle = isset($el['style']) ? $el['style'] : 'pills';
                $listClass = ($tabStyle === 'pills') ? 'nav-pills gap-2 mb-4' : 'nav-tabs mb-4';
                $linkClass = ($tabStyle === 'pills') ? 'rounded-pill px-4 py-2 border' : '';
                
                echo "<ul class=\"nav {$listClass}\" id=\"{$el['id']}\" role=\"tablist\">";
                $first = true;
                foreach ($el['tabs'] as $tid => $tab) {
                    $activeClass = ($first) ? 'active' : '';
                    $selected = ($first) ? 'true' : 'false';
                    $icon = isset($tab['icon']) ? "<i class=\"{$tab['icon']} me-2\"></i> " : "";
                    
                    echo "<li class=\"nav-item\" role=\"presentation\">
                            <button class=\"nav-link fw-black text-uppercase tracking-widest fs-9 {$linkClass} {$activeClass}\" 
                                    id=\"tab-{$tid}\" data-bs-toggle=\"tab\" data-bs-target=\"#content-{$tid}\" 
                                    type=\"button\" role=\"tab\" aria-controls=\"content-{$tid}\" aria-selected=\"{$selected}\">
                                {$icon}{$tab['label']}
                            </button>
                          </li>";
                    $first = false;
                }
                echo '</ul>';
                break;

            case 'tab_content':
                $active = (isset($el['active']) && $el['active']) ? 'show active' : '';
                echo "<div class=\"tab-pane fade {$active}\" id=\"content-{$el['id']}\" role=\"tabpanel\" aria-labelledby=\"tab-{$el['id']}\">";
                if (isset($el['body_elements'])) {
                    foreach ($el['body_elements'] as $bel) {
                        $this->renderElement($bel);
                    }
                }
                echo '</div>';
                break;

            case 'table':
                $headers = $el['headers'] ?? [];
                $rows = $el['rows'] ?? [];
                echo '<div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">';
                if ($headers) {
                    echo '<thead class="bg-light bg-opacity-50 text-muted extra-small text-uppercase tracking-widest fw-bold" style="font-size:0.7rem;"><tr>';
                    foreach ($headers as $th) {
                        $thContent = is_array($th) ? ($th['content'] ?? '') : $th;
                        $thClass = is_array($th) ? ($th['class'] ?? 'ps-3 py-3') : 'ps-3 py-3';
                        echo "<th class=\"{$thClass}\">{$thContent}</th>";
                    }
                    echo '</tr></thead>';
                }
                echo '<tbody>';
                if (empty($rows)) {
                    $colspan = count($headers) ?: 1;
                    $emptyMsg = $el['empty_message'] ?? 'No data found.';
                    echo "<tr><td colspan=\"{$colspan}\" class=\"text-center py-5\"><p class=\"text-muted fw-bold mb-0\">{$emptyMsg}</p></td></tr>";
                } else {
                    foreach ($rows as $tr) {
                        echo '<tr>';
                        foreach ($tr as $td) {
                            $tdContent = is_array($td) ? ($td['content'] ?? '') : $td;
                            $tdClass = is_array($td) ? ($td['class'] ?? 'ps-3 py-3') : 'ps-3 py-3';
                            echo "<td class=\"{$tdClass}\">{$tdContent}</td>";
                        }
                        echo '</tr>';
                    }
                }
                echo '</tbody></table></div>';
                break;

            case 'form':
                $action = $el['action'] ?? '';
                $method = $el['method'] ?? 'post';
                $attr = $el['attr'] ?? '';
                echo "<form action=\"{$action}\" method=\"{$method}\" {$attr}>";
                if (isset($el['hidden'])) {
                    foreach ($el['hidden'] as $n => $v) {
                        $nv = htmlspecialchars($n);
                        $vv = htmlspecialchars($v);
                        echo "<input type=\"hidden\" name=\"{$nv}\" value=\"{$vv}\">";
                    }
                }
                foreach ($el['fields'] ?? [] as $f) {
                    $this->renderElement($f);
                }
                echo "</form>";
                break;

            case 'input':
                $lbl = isset($el['label']) ? "<label class=\"form-label fw-black text-muted extra-small text-uppercase tracking-wider\" style=\"font-size:0.65rem;\">{$el['label']}</label>" : '';
                $type = $el['input_type'] ?? 'text';
                $name = $el['name'] ?? '';
                $val = htmlspecialchars((string)($el['value'] ?? ''));
                $plc = htmlspecialchars((string)($el['placeholder'] ?? ''));
                $req = !empty($el['required']) ? 'required' : '';
                echo "<div class=\"mb-4\">{$lbl}<input type=\"{$type}\" name=\"{$name}\" class=\"form-control rounded-4 bg-light shadow-none border py-2 px-3 fs-8 fw-bold\" value=\"{$val}\" placeholder=\"{$plc}\" {$req}></div>";
                break;

            case 'textarea':
                $lbl = isset($el['label']) ? "<label class=\"form-label fw-black text-muted extra-small text-uppercase tracking-wider\" style=\"font-size:0.65rem;\">{$el['label']}</label>" : '';
                $name = $el['name'] ?? '';
                $val = htmlspecialchars((string)($el['value'] ?? ''));
                $cls = $el['class'] ?? 'form-control bg-light shadow-none border py-2 px-3 fs-8 fw-bold';
                $rows = $el['rows'] ?? 5;
                $radius = strpos($cls, 'editor') !== false ? 'rounded-2' : 'rounded-4';
                echo "<div class=\"mb-4\">{$lbl}<textarea name=\"{$name}\" class=\"{$cls} {$radius}\" rows=\"{$rows}\">{$val}</textarea></div>";
                break;

            case 'select':
                $lbl = isset($el['label']) ? "<label class=\"form-label fw-black text-muted extra-small text-uppercase tracking-wider\" style=\"font-size:0.65rem;\">{$el['label']}</label>" : '';
                $name = $el['name'] ?? '';
                echo "<div class=\"mb-4\">{$lbl}<select name=\"{$name}\" class=\"form-select rounded-4 bg-light shadow-none border py-2 px-3 fs-8 fw-bold\">";
                foreach ($el['options'] ?? [] as $v => $l) {
                    $sel = (isset($el['selected']) && (string)$el['selected'] === (string)$v) ? 'selected' : '';
                    echo "<option value=\"{$v}\" {$sel}>{$l}</option>";
                }
                echo "</select>";
                if (isset($el['help'])) {
                    echo "<div class=\"form-text text-muted small mt-2\">{$el['help']}</div>";
                }
                echo "</div>";
                break;

            case 'button':
                $btnType = $el['btn_type'] ?? 'submit';
                $name = isset($el['name']) ? "name=\"{$el['name']}\"" : "";
                $cls = $el['class'] ?? 'btn btn-primary btn-lg rounded-pill fw-bold shadow-sm px-4';
                $icon = isset($el['icon']) ? "<i class=\"{$el['icon']} me-2\"></i>" : '';
                $lbl = $el['label'] ?? 'Submit';
                echo "<button type=\"{$btnType}\" {$name} class=\"{$cls}\">{$icon}{$lbl}</button>";
                break;

            case 'alert':
                $style = $el['style'] ?? 'info';
                $content = $el['content'] ?? '';
                $dismissible = isset($el['dismissible']) && $el['dismissible'] ? 'alert-dismissible fade show' : '';
                echo "<div class=\"alert alert-{$style} rounded-4 border-0 shadow-sm {$dismissible}\" role=\"alert\">";
                echo $content;
                if ($dismissible) {
                    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                }
                echo "</div>";
                break;

            case 'badge':
                $color = $el['color'] ?? 'primary';
                $text = $el['text'] ?? '';
                $pill = isset($el['pill']) && !$el['pill'] ? '' : 'rounded-pill';
                echo "<span class=\"badge bg-{$color} bg-opacity-10 text-{$color} px-3 {$pill} fw-bold small\">{$text}</span>";
                break;

            case 'progress':
                $color = $el['color'] ?? 'primary';
                $val = $el['value'] ?? 0;
                $lbl = $el['label'] ?? '';
                echo "<div class=\"mb-3\">
                        <div class=\"d-flex justify-content-between mb-1\">
                            <span class=\"small fw-bold text-muted\">{$lbl}</span>
                            <span class=\"small fw-bold text-{$color}\">{$val}%</span>
                        </div>
                        <div class=\"progress rounded-pill\" style=\"height: 8px;\">
                            <div class=\"progress-bar bg-{$color} rounded-pill\" role=\"progressbar\" style=\"width: {$val}%\"></div>
                        </div>
                      </div>";
                break;

            case 'list_group':
                echo '<div class="list-group list-group-flush">';
                foreach ($el['items'] ?? [] as $item) {
                    $icon = isset($item['icon']) ? "<i class=\"{$item['icon']} me-3 text-muted\"></i>" : '';
                    $active = isset($item['active']) && $item['active'] ? 'active' : '';
                    echo "<div class=\"list-group-item d-flex align-items-center bg-transparent px-0 py-3 {$active}\">
                            {$icon}
                            <div class=\"flex-fill\">
                                <div class=\"fw-bold text-dark mb-0\">" . ($item['title'] ?? '') . "</div>
                                <div class=\"extra-small text-muted\">" . ($item['subtitle'] ?? '') . "</div>
                            </div>";
                    if (isset($item['badge'])) {
                        $this->renderElement(['type' => 'badge', 'text' => $item['badge'], 'color' => $item['badge_color'] ?? 'primary']);
                    }
                    echo "</div>";
                }
                echo '</div>';
                break;

            case 'breadcrumb':
                echo '<nav aria-label="breadcrumb"><ol class="breadcrumb mt-n2 mb-3 bg-transparent p-0 small">';
                foreach ($el['items'] ?? [] as $item) {
                    $act = (isset($item['active']) && $item['active']) ? 'active text-muted fw-bold' : '';
                    $u = $item['url'] ?? '#';
                    if ($act) {
                        echo "<li class=\"breadcrumb-item {$act}\" aria-current=\"page\">{$item['label']}</li>";
                    } else {
                        echo "<li class=\"breadcrumb-item\"><a href=\"{$u}\" class=\"text-decoration-none text-primary fw-medium\">{$item['label']}</a></li>";
                    }
                }
                echo '</ol></nav>';
                break;

            case 'dropdown_button':
                $cls = $el['class'] ?? 'btn btn-white border px-3 rounded-pill shadow-sm fw-bold small';
                $label = $el['label'] ?? 'Actions';
                $align = isset($el['align']) && $el['align'] == 'end' ? 'dropdown-menu-end' : '';
                echo "<div class=\"dropdown d-inline-block\">
                        <button class=\"{$cls} dropdown-toggle\" type=\"button\" data-bs-toggle=\"dropdown\">{$label}</button>
                        <ul class=\"dropdown-menu {$align} border-0 shadow-lg p-3 rounded-4\" style=\"min-width: 250px;\">";
                foreach ($el['items'] ?? [] as $it) {
                    $type = $it['type'] ?? 'link';
                    if ($type === 'header') {
                        echo "<li><h6 class=\"dropdown-header px-2 extra-small text-uppercase fw-black text-muted tracking-widest mb-2\">{$it['label']}</h6></li>";
                    } elseif ($type === 'divider') {
                        echo "<li><hr class=\"dropdown-divider mx-1 opacity-10\"></li>";
                    } elseif ($type === 'raw' || $type === 'html') {
                        echo "<li><div class=\"px-2 py-1\">{$it['label']}</div></li>";
                    } else {
                        $u = $it['url'] ?? '#';
                        $ic = isset($it['icon']) ? "<i class=\"{$it['icon']} me-2 opacity-50\"></i>" : '';
                        echo "<li><a class=\"dropdown-item rounded-3 small py-2 fw-medium\" href=\"{$u}\">{$ic}{$it['label']}</a></li>";
                    }
                }
                echo "</ul></div>";
                break;

            case 'search_group':
                $action = $el['action'] ?? '';
                $name = $el['name'] ?? 'q';
                $val = htmlspecialchars((string)($el['value'] ?? ''));
                $plc = htmlspecialchars((string)($el['placeholder'] ?? 'Search...'));
                echo "<form method=\"get\" action=\"{$action}\" class=\"d-flex gap-2\">";
                if (isset($el['hidden'])) {
                    foreach ($el['hidden'] as $hn => $hv) {
                        echo "<input type=\"hidden\" name=\"".htmlspecialchars($hn)."\" value=\"".htmlspecialchars($hv)."\">";
                    }
                }
                echo "  <div class=\"input-group shadow-sm rounded-pill overflow-hidden border bg-white\">
                            <span class=\"input-group-text bg-white border-0 ps-3\"><i class=\"bi bi-search text-muted small\"></i></span>
                            <input type=\"text\" name=\"{$name}\" class=\"form-control border-0 ps-1 shadow-none small\" placeholder=\"{$plc}\" value=\"{$val}\">
                            <button class=\"btn btn-white border-0 fw-bold px-3 small\" type=\"submit\">Search</button>
                        </div>
                      </form>";
                break;

            case 'heading':
                $text = $el['text'] ?? '';
                $icon = isset($el['icon']) ? "<i class=\"{$el['icon']} text-primary me-2\"></i>" : '';
                $cls = $el['class'] ?? 'fw-bold text-dark mb-4';
                $sub = isset($el['subtitle']) ? "<p class=\"extra-small text-muted fw-bold text-uppercase tracking-widest mt-n3 mb-4\" style=\"font-size:0.6rem; opacity:0.7;\">{$el['subtitle']}</p>" : "";
                echo "<h5 class=\"{$cls}\">{$icon}{$text}</h5>{$sub}";
                break;

            case 'pagination':
                $html = $el['html'] ?? '';
                echo "<div class=\"pagination-wrapper mt-4\">{$html}</div>";
                break;

            case 'bulk_actions':
                $options = $el['options'] ?? [];
                echo "<div class=\"d-flex align-items-center gap-2\">
                        <select name=\"action\" class=\"form-select form-select-sm rounded-pill bg-light shadow-none border\" style=\"width: 160px;\">";
                foreach ($options as $v => $l) {
                    echo "<option value=\"{$v}\">" . _($l) . "</option>";
                }
                echo "  </select>
                        <button type=\"submit\" name=\"doaction\" class=\"btn btn-danger btn-sm rounded-pill px-4 shadow-sm fw-bold\">
                            <i class=\"bi bi-lightning-fill me-1\"></i> " . ($el['button_label'] ?? _('Execute')) . "
                        </button>
                      </div>";
                break;

            case 'modal':
                $id = $el['id'] ?? 'modal-id';
                $title = $el['title'] ?? 'Modal';
                $size = $el['size'] ?? 'lg';
                echo "<div class=\"modal fade\" id=\"{$id}\" tabindex=\"-1\" aria-hidden=\"true\">
                        <div class=\"modal-dialog modal-dialog-centered modal-{$size}\">
                            <div class=\"modal-content border-0 shadow-lg rounded-4 overflow-hidden\">";
                if (isset($el['header'])) {
                    echo "<div class=\"modal-header border-0 py-4 px-5\">
                            <h5 class=\"modal-title fw-bold text-dark\">{$el['header']}</h5>
                            <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\" aria-label=\"Close\"></button>
                          </div>";
                }
                echo "<div class=\"modal-body p-5\">";
                foreach ($el['body_elements'] ?? [] as $bel) {
                    $this->renderElement($bel);
                }
                echo "</div>";
                if (isset($el['footer'])) {
                    echo "<div class=\"modal-footer border-0 p-5 pt-0\">{$el['footer']}</div>";
                }
                echo "</div></div></div>";
                break;

            case 'accordion':
                $id = $el['id'] ?? 'accordion-id';
                echo "<div class=\"accordion\" id=\"{$id}\">";
                foreach ($el['items'] ?? [] as $i => $item) {
                    $itemId = "{$id}-{$i}";
                    $show = (isset($item['active']) && $item['active']) ? 'show' : '';
                    $collapsed = (isset($item['active']) && $item['active']) ? '' : 'collapsed';
                    echo "<div class=\"accordion-item border-0 border-bottom\">
                            <h2 class=\"accordion-header\" id=\"heading-{$itemId}\">
                                <button class=\"accordion-button {$collapsed} bg-white py-4 px-4 shadow-none\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#collapse-{$itemId}\">
                                    " . ($item['header_html'] ?? '') . "
                                </button>
                            </h2>
                            <div id=\"collapse-{$itemId}\" class=\"accordion-collapse collapse {$show}\" data-bs-parent=\"#{$id}\">
                                <div class=\"accordion-body bg-light bg-opacity-50 p-4\">";
                    foreach ($item['body_elements'] ?? [] as $bel) {
                        $this->renderElement($bel);
                    }
                    echo "      </div>
                            </div>
                          </div>";
                }
                echo "</div>";
                break;

            case 'grid':
                $cls = $el['class'] ?? 'row g-4';
                echo "<div class=\"{$cls}\">";
                foreach ($el['content'] ?? [] as $cel) $this->renderElement($cel);
                echo '</div>';
                break;
            case 'column':
            case 'col':
                $cls = $el['class'] ?? 'col-md-12';
                echo "<div class=\"{$cls}\">";
                foreach ($el['content'] ?? [] as $cel) $this->renderElement($cel);
                echo '</div>';
                break;

            case 'html': // alias for raw
            case 'raw':
            default:
                echo $el['html'] ?? '';
                break;
        }

        if ($return) {
            return ob_get_clean();
        }
        return '';
    }
}
