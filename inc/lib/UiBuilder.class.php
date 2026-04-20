<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - UiBuilder Class
 *
 * A core class to build dynamic backend web elements based on arrays.
 * Operates similarly to OptionsBuilder but is designed for generic backend
 * module pages (dashboards, tables, forms, stat cards) rather than just options.
 *
 * @since 2.0.0 build date 2026
 * @version 2.3.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class UiBuilder
{
    private array $schema;
    private string $activeTab;

    /**
     * UiBuilder Constructor.
     *
     * @param array $schema The UI schema definition defining tabs, headers, and elements.
     */
    public function __construct(array $schema = [])
    {
        $this->schema = $schema;
        $this->activeTab = isset($_GET['tab']) ? Typo::cleanX($_GET['tab']) : ($schema['default_tab'] ?? '');
    }

    /**
     * Renders the module header including title, subtitle, and action buttons.
     */
    public function renderHeader(): void
    {
        if (empty($this->schema['header'])) {
            return;
        }

        $h = $this->schema['header'];
        $title = $h['title'] ?? 'Dashboard';
        $subtitle = $h['subtitle'] ?? '';
        $icon = $h['icon'] ?? '';
        $button = $h['button'] ?? null;
        $buttons = $h['buttons'] ?? [];

        // Dynamic Header Styling
        echo '<style>
            .ui-header { transition: all 0.3s ease; z-index: 1010; top: 60px; position: sticky; margin-bottom: 2rem; background: transparent; }
            .ui-header.is-scrolled { background: white !important; padding-top: 0.75rem !important; padding-bottom: 0.75rem !important; border-bottom: 1px solid #eee !important; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.05) !important; margin-left: -1rem !important; margin-right: -1rem !important; padding-left: 1rem !important; padding-right: 1rem !important; }
            .ui-header .header-icon-bg { transition: all 0.3s ease; width: 48px; height: 48px; }
            .ui-header.is-scrolled .header-icon-bg { width: 32px !important; height: 32px !important; }
            .ui-header.is-scrolled .header-icon-bg i { font-size: 0.9rem !important; }
            .ui-header h2 { transition: all 0.3s ease; font-size: 2rem; }
            .ui-header.is-scrolled h2 { font-size: 1.25rem !important; }
            .ui-header .ui-header-subtitle { transition: all 0.3s ease; }
            .ui-header.is-scrolled .ui-header-subtitle { font-size: 0.65rem !important; opacity: 0.8; }
        </style>';

        echo '<div id="uiStickyHeader" class="row align-items-center ui-header py-3">';
        echo '  <div class="col-md-6">';
        echo '      <div class="d-flex align-items-center">';
        if ($icon) {
            echo '      <div class="header-icon-bg bg-primary bg-opacity-10 rounded-pill d-flex align-items-center justify-content-center me-3">';
            echo '          <i class="' . $icon . ' fs-4 text-primary"></i>';
            echo '      </div>';
        }
        echo '          <div>';
        echo '              <h2 class="fw-black text-dark mb-0">' . $title . '</h2>';
        if ($subtitle) {
            echo '          <p class="text-muted mb-0 extra-small ui-header-subtitle">' . $subtitle . '</p>';
        }
        echo '          </div>';
        echo '      </div>';
        echo '  </div>';

        if ($button || !empty($buttons)) {
            echo '  <div class="col-md-6 text-end">';
            if (!empty($buttons)) {
                foreach ($buttons as $btn) {
                    $this->renderButton($btn);
                }
            } else if ($button) {
                $this->renderButton($button);
            }
            echo '  </div>';
        }

        echo '</div>';

        echo '<script>
            window.addEventListener("scroll", function() {
                var header = document.getElementById("uiStickyHeader");
                if (window.scrollY > 20) {
                    header.classList.add("is-scrolled");
                } else {
                    header.classList.remove("is-scrolled");
                }
            });
        </script>';
    }

    private function renderButton(array $btn): void
    {
        $label = $btn['label'] ?? 'Button';
        $class = $btn['class'] ?? 'btn btn-primary rounded-pill px-4 fw-bold shadow-sm';
        $icon = $btn['icon'] ?? '';
        $type = $btn['type'] ?? 'button';
        $attr = $btn['attr'] ?? '';
        $name = $btn['name'] ?? '';
        $form = $btn['form'] ?? '';
        $href = $btn['href'] ?? ($btn['url'] ?? '#');

        $formAttr = $form ? 'form="' . $form . '"' : '';
        $nameAttr = $name ? 'name="' . $name . '"' : '';

        if ($type === 'link') {
            echo '<a href="' . $href . '" class="' . $class . '" ' . $attr . '>';
        } else {
            $btnRealType = $btn['btn_type'] ?? 'submit';
            echo '<button type="' . $btnRealType . '" ' . $nameAttr . ' ' . $formAttr . ' class="' . $class . '" ' . $attr . '>';
        }

        if ($icon) {
            echo '<i class="' . $icon . ' me-1"></i> ';
        }
        echo $label;

        if ($type === 'link') {
            echo '</a>';
        } else {
            echo '</button>';
        }
        echo ' ';
    }

    /**
     * Orchestrates the rendering of the entire module page.
     * Handles containers, headers, tab navigation, and tab content.
     */
    public function render(): void
    {
        echo '<div class="container-fluid p-0">';

        // Render Header (Matches GeniXCMS Admin Style)
        $this->renderHeader();

        // Render Tabs Navigation as Bootstrap 5 Pills
        if (isset($this->schema['tabs']) && count($this->schema['tabs']) > 0) {
            $tabMode = $this->schema['tab_mode'] ?? 'link';
            $tabStyle = $this->schema['tab_style'] ?? 'pills';

            $tabClass = match ($tabStyle) {
                'tabs' => 'nav-tabs border-0 px-4 pt-4',
                'modern' => 'nav-tabs border-0 border-bottom mb-5 modern-tabs',
                default => 'nav-pills gap-2 mb-4'
            };

            $linkClass = match ($tabStyle) {
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
                if ($tabStyle === 'pills' && $this->activeTab === $id)
                    $activeClass .= ' shadow-sm';

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
                    $url = $tab['url'] ?? "index.php?page=" . ($_GET['page'] ?? '') . (isset($_GET['mod']) ? "&mod=" . $_GET['mod'] : "") . "&tab={$id}";
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

                // Render direct content/modals even if tabs are used (for JS mode)
                if (isset($this->schema['content'])) {
                    foreach ($this->schema['content'] as $element) {
                        $this->renderElement($element);
                    }
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
        }

        // Render direct content/modals (for non-JS tab mode or no tabs)
        if (isset($this->schema['content'])) {
            foreach ($this->schema['content'] as $element) {
                $this->renderElement($element);
            }
        }

        echo '</div>'; // container-fluid

        // ── RENDER REACTIVITY SCRIPT ──────────────────────────────────
        $this->renderReactivityScript();
    }

    /**
     * Renders the JavaScript responsible for element dependencies (require).
     */
    private function renderReactivityScript(): void
    {
        echo '<script>
            (function() {
                const initReactivity = function() {
                    const dependentElements = document.querySelectorAll("[data-ui-require]");
                    
                    dependentElements.forEach(el => {
                        const targetName = el.getAttribute("data-ui-require");
                        const requiredValue = el.getAttribute("data-ui-require-value");
                        const action = el.getAttribute("data-ui-require-action");
                        
                        // Find target element by ID or Name
                        let target = document.getElementById(targetName) || document.querySelector(`[name="${targetName}"]`) || document.querySelector(`[name="${targetName}[]"]`);
                        
                        if (!target) return;

                        const updateState = () => {
                            let currentValue;
                            const targetElements = document.getElementsByName(targetName);
                            const targetElement = document.getElementById(targetName) || (targetElements.length > 0 ? targetElements[0] : null);

                            if (!targetElement && targetElements.length === 0) return;

                            if (targetElement && (targetElement.type === "checkbox" || targetElement.type === "radio")) {
                                if (targetElements.length > 1) {
                                    // Handle Radio Group
                                    const checkedRadio = Array.from(targetElements).find(r => r.checked);
                                    currentValue = checkedRadio ? checkedRadio.value : "";
                                } else {
                                    // Handle Single Checkbox
                                    currentValue = targetElement.checked ? (targetElement.value || "on") : "off";
                                }
                            } else {
                                currentValue = targetElement ? targetElement.value : "";
                            }

                            const isMet = (requiredValue === "*" && currentValue !== "" && currentValue !== "0") || (currentValue === requiredValue);
                            
                            if (action === "show") {
                                el.style.display = isMet ? "" : "none";
                            } else if (action === "enable") {
                                const inputs = el.querySelectorAll("input, select, textarea, button");
                                if (el.tagName === "INPUT" || el.tagName === "SELECT" || el.tagName === "TEXTAREA" || el.tagName === "BUTTON") {
                                    el.disabled = !isMet;
                                }
                                inputs.forEach(i => i.disabled = !isMet);
                            }

                            // Handle AJAX Reload if applicable
                            const ajaxUrl = el.getAttribute("data-ui-ajax-url");
                            if (ajaxUrl && isMet && currentValue !== el.getAttribute("data-last-val")) {
                                el.setAttribute("data-last-val", currentValue);
                                const select = el.querySelector("select");
                                if (select) {
                                    select.innerHTML = "<option>Loading...</option>";
                                    fetch(ajaxUrl + "&val=" + currentValue)
                                    .then(r => r.json())
                                    .then(data => {
                                        let html = "";
                                        if (Object.keys(data).length > 0) {
                                            for (const [v, l] of Object.entries(data)) {
                                                html += `<option value="${v}">${l}</option>`;
                                            }
                                        } else {
                                            html = "<option value=\"0\">No options found</option>";
                                        }
                                        select.innerHTML = html;
                                    })
                                    .catch(e => {
                                        console.error("AJAX Load Error", e);
                                        select.innerHTML = "<option value=\"0\">Error loading options</option>";
                                    });
                                }
                            }
                        };

                        const targets = document.getElementsByName(targetName);
                        const idTarget = document.getElementById(targetName);
                        
                        if (idTarget) {
                            idTarget.addEventListener("change", updateState);
                            idTarget.addEventListener("input", updateState);
                        }
                        targets.forEach(t => {
                            t.addEventListener("change", updateState);
                            t.addEventListener("input", updateState);
                        });
                        
                        // Run once on init
                        updateState();
                    });
                };

                if (document.readyState === "loading") {
                    document.addEventListener("DOMContentLoaded", initReactivity);
                } else {
                    initReactivity();
                }
            })();
        </script>';
    }

    /**
     * Recursively renders a single UI element based on its type.
     * Supports a wide variety of types including stats cards, tables, forms, charts, and layout containers.
     *
     * @param array $el The element definition.
     * @param bool $return If true, returns the HTML as a string instead of echoing.
     * @return string The rendered HTML if $return is true, otherwise an empty string.
     */
    public function renderElement(array $el, bool $return = false): string
    {
        if ($return) {
            ob_start();
        }
        $type = $el['type'] ?? 'raw';

        // ── DEPENDENCY HANDLING ──────────────────────────────────────
        $requireAttr = '';
        if (isset($el['require'])) {
            $requireAction = $el['require_action'] ?? 'show'; // show, enable, disable
            $requireValue = $el['require_value'] ?? '*';
            $requireAttr = ' data-ui-require="' . $el['require'] . '" data-ui-require-value="' . $requireValue . '" data-ui-require-action="' . $requireAction . '"';
            
            if (isset($el['ajax_url'])) {
                $requireAttr .= ' data-ui-ajax-url="' . $el['ajax_url'] . '"';
            }

            // Initial state for 'show' action: hidden until triggered
            if ($requireAction === 'show' && $requireValue !== '*') {
                $requireAttr .= ' style="display: none;"';
            }
        }

        switch ($type) {
            case 'row':
                echo '<div class="row g-4 mb-4"' . $requireAttr . '>';
                foreach ($el['items'] ?? [] as $col) {
                    $w = $col['width'] ?? 12;
                    $class = $col['class'] ?? "col-lg-{$w}";
                    echo "<div class=\"{$class}\">";
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
                $colors = ['primary', 'success', 'warning', 'info', 'danger', 'dark'];
                $size = $el['size'] ?? 'normal';
                $style = $el['style'] ?? 'classic'; // classic, modern

                foreach ($el['items'] ?? [] as $i => $stat) {
                    $w = $stat['width'] ?? ($size === 'small' ? 2 : 3);
                    $color = $stat['color'] ?? $colors[$i % count($colors)];
                    $icon = $stat['icon'] ?? 'bi bi-reception-4';
                    $val = $stat['value'] ?? '0';
                    $lbl = $stat['label'] ?? '';

                    if ($size === 'small') {
                        echo "
                        <div class=\"col-xl-{$w} col-md-4 col-6\">
                            <div class=\"card border-0 shadow-sm rounded-5 h-100 p-0 overflow-hidden position-relative border-start border-4 border-{$color}\">
                                <div class=\"card-body p-3\">
                                    <div class=\"d-flex align-items-center gap-3\">
                                        <div class=\"stats-icon-bg bg-{$color} bg-opacity-10 rounded-pill d-flex align-items-center justify-content-center flex-shrink-0\" style=\"width:40px; height:40px;\">
                                            <i class=\"{$icon} fs-5 text-{$color}\"></i>
                                        </div>
                                        <div class=\"overflow-hidden\">
                                            <div class=\"extra-small fw-bold text-muted text-uppercase tracking-wider mb-0 truncation-ellipsis\" style=\"font-size:0.6rem; letter-spacing:0.05em;\">{$lbl}</div>
                                            <h4 class=\"fw-black m-0 lh-1\" style=\"font-weight:900 !important;\">{$val}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>";
                    } else if ($style === 'modern') {
                        $txtColor = in_array($color, ['warning', 'light', 'white']) ? 'text-dark' : 'text-white';
                        $opacity = in_array($color, ['warning', 'light', 'white']) ? 'opacity-75' : 'opacity-75';
                        echo "
                        <div class=\"col-xl-{$w} col-md-6\">
                            <div class=\"card border-0 shadow-sm rounded-5 h-100 p-0 overflow-hidden position-relative bg-{$color} {$txtColor}\">
                                <div class=\"card-body p-4\">
                                    <div class=\"d-flex justify-content-between align-items-start\">
                                        <div>
                                            <div class=\"small fw-bold {$opacity} text-uppercase tracking-wider mb-1\" style=\"font-size:0.75rem;\">{$lbl}</div>
                                            <h3 class=\"fw-black m-0 fs-3\" style=\"font-weight:900 !important;\">{$val}</h3>
                                        </div>
                                        <div class=\"stats-icon-bg bg-white rounded-4 d-flex align-items-center justify-content-center\" style=\"width:54px; height:54px;\">
                                            <i class=\"{$icon} fs-2 text-{$color}\"></i>
                                        </div>
                                    </div>";
                        if (isset($stat['footer_link'])) {
                            echo "<div class=\"d-flex align-items-center justify-content-between mt-3 pt-3 border-top border-white border-opacity-10\">
                                    <div class=\"extra-small small fw-bold {$opacity}\">" . ($stat['footer_text'] ?? '') . "</div>
                                    <a href=\"{$stat['footer_link']}\" class=\"btn btn-white btn-sm rounded-pill px-3 fs-8 fw-bold py-1\">
                                        Details <i class=\"bi bi-chevron-right ms-1\"></i>
                                    </a>
                                  </div>";
                        }
                        echo "  </div>
                            </div>
                        </div>";
                    } else {
                        // Classic Style
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
                            echo "<div class=\"d-flex align-items-center justify-content-between mt-3\">
                                    <div class=\"extra-small text-muted fw-bold\">" . ($stat['footer_text'] ?? '') . "</div>
                                    <a href=\"{$stat['footer_link']}\" class=\"btn btn-light btn-sm rounded-pill px-3 fs-8 fw-bold\">
                                        View All <i class=\"bi bi-chevron-right ms-1\"></i>
                                    </a>
                                  </div>";
                        }

                        echo "  </div>
                                <div class=\"position-absolute bottom-0 end-0\" style=\"width:100px;height:100px;background:currentColor;opacity:0.03;border-radius:50%;margin-right:-20px;margin-bottom:-20px;pointer-events:none;\"></div>
                            </div>
                        </div>";
                    }
                }
                echo '</div>';
                break;

            case 'card':
                $h100 = (isset($el['full_height']) && $el['full_height']) ? 'h-100' : '';
                echo '<div class="card border-0 shadow-sm rounded-5 mb-4 ' . $h100 . (isset($el['class']) ? " " . $el['class'] : "") . '">';

                if (isset($el['title'])) {
                    $iconHtml = "";
                    if (isset($el['icon'])) {
                        $iconHtml = '
                        <div class="bg-primary bg-opacity-10 rounded-pill d-flex align-items-center justify-content-center me-3" style="width:38px; height:38px; flex-shrink:0;">
                            <i class="' . $el['icon'] . ' text-primary fs-5"></i>
                        </div>';
                    }
                    $sub = isset($el['subtitle']) ? "<p class=\"extra-small text-muted mb-0\" style=\"font-size:0.75rem;\">{$el['subtitle']}</p>" : "";
                    echo "<div class=\"card-header bg-white border-0 pt-4 pb-2 px-4\">
                            <div class=\"d-flex flex-wrap gap-3 justify-content-between align-items-center\">
                                <div class=\"d-flex align-items-center\">
                                    {$iconHtml}
                                    <div>
                                        <h5 class=\"fw-bold text-dark m-0 d-flex align-items-center\">{$el['title']}</h5>
                                        {$sub}
                                    </div>
                                </div>";
                    if (isset($el['header_action'])) {
                        echo '<div class="ms-md-auto">';
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

                if (isset($el['footer']) || isset($el['footer_elements'])) {
                    $footerContent = $el['footer'] ?? '';
                    $footerElements = $el['footer_elements'] ?? [];
                    $footerNoPadding = $el['footer_no_padding'] ?? false;
                    $footerClass = $el['footer_class'] ?? 'card-footer bg-light bg-opacity-50 border-0 py-3 border-top';

                    echo "<div class=\"{$footerClass}\">";
                    if (!empty($footerElements) || is_array($footerContent)) {
                        echo '<div class="d-flex flex-wrap align-items-center w-100 gap-3 ' . ($footerNoPadding ? 'p-0' : 'px-2') . '">';
                        
                        $toRender = !empty($footerElements) ? $footerElements : $footerContent;
                        foreach ($toRender as $fSub) {
                            $wrapperClass = "";
                            if (is_array($fSub) && isset($fSub['type']) && $fSub['type'] === 'pagination') {
                                $wrapperClass = "ms-auto";
                            }
                            
                            echo "<div class=\"{$wrapperClass}\">";
                            if (is_array($fSub)) {
                                $this->renderElement($fSub);
                            } else {
                                echo $fSub;
                            }
                            echo '</div>';
                        }
                        
                        echo '</div>';
                    } else {
                        echo "<div class=\"text-center\">{$footerContent}</div>";
                    }
                    echo "</div>";
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
            case 'text':
            case 'number':
            case 'email':
            case 'date':
            case 'password':
            case 'url':
            case 'color':
            case 'file':
            case 'hidden':
                $lbl = isset($el['label']) ? "<label class=\"form-label fw-bold text-dark small\">{$el['label']}</label>" : '';
                $inputType = $el['input_type'] ?? ($type === 'input' ? 'text' : $type);
                $name = $el['name'] ?? '';
                $val = htmlspecialchars((string) ($el['value'] ?? ''));
                $plc = htmlspecialchars((string) ($el['placeholder'] ?? ''));
                $req = !empty($el['required']) ? 'required' : '';
                $help = isset($el['help']) ? "<div class=\"form-text text-muted small mt-1\">{$el['help']}</div>" : '';
                $wrap = $el['wrapper_class'] ?? 'mb-3';
                $idAttr = isset($el['id']) ? "id=\"{$el['id']}\"" : "";
                
                // Initial state for 'enable' action
                $disabledAttr = '';
                if (isset($el['require']) && ($el['require_action'] ?? 'show') === 'enable') {
                    $disabledAttr = ' disabled';
                }

                echo "<div class=\"{$wrap}\" {$requireAttr}>{$lbl}<input type=\"{$inputType}\" name=\"{$name}\" {$idAttr} class=\"form-control rounded-4 bg-light shadow-none border py-2 px-3 fs-8 fw-bold\" value=\"{$val}\" placeholder=\"{$plc}\" {$req} {$disabledAttr}>{$help}</div>";
                break;

            case 'textarea':
                $lbl = isset($el['label']) ? "<label class=\"form-label fw-bold text-dark small\">{$el['label']}</label>" : '';
                $name = $el['name'] ?? '';
                $val = htmlspecialchars((string) ($el['value'] ?? ''));
                $cls = $el['class'] ?? 'form-control bg-light shadow-none border py-2 px-3 fs-8 fw-bold';
                $rows = $el['rows'] ?? 5;
                $radius = strpos($cls, 'editor') !== false ? 'rounded-2' : 'rounded-4';
                $help = isset($el['help']) ? "<div class=\"form-text text-muted small mt-1\">{$el['help']}</div>" : '';
                $wrap = $el['wrapper_class'] ?? 'mb-3';
                $idAttr = isset($el['id']) ? "id=\"{$el['id']}\"" : "";

                // Initial state for 'enable' action
                $disabledAttr = '';
                if (isset($el['require']) && ($el['require_action'] ?? 'show') === 'enable') {
                    $disabledAttr = ' disabled';
                }

                echo "<div class=\"{$wrap}\" {$requireAttr}>{$lbl}<textarea name=\"{$name}\" {$idAttr} class=\"{$cls} {$radius}\" rows=\"{$rows}\" {$disabledAttr}>{$val}</textarea>{$help}</div>";
                break;

            case 'select':
            case 'dropdown':
                $lbl = isset($el['label']) ? "<label class=\"form-label fw-bold text-dark small\">{$el['label']}</label>" : '';
                $name = $el['name'] ?? '';
                $wrap = $el['wrapper_class'] ?? 'mb-3';
                $idAttr = isset($el['id']) ? "id=\"{$el['id']}\"" : "";
                $cls = $el['class'] ?? 'form-select rounded-4 bg-light shadow-none border py-2 px-3 fs-8 fw-bold';

                // Initial state for 'enable' action
                $disabledAttr = '';
                if (isset($el['require']) && ($el['require_action'] ?? 'show') === 'enable') {
                    $disabledAttr = ' disabled';
                }

                echo "<div class=\"{$wrap}\" {$requireAttr}>{$lbl}<select name=\"{$name}\" {$idAttr} class=\"{$cls}\" {$disabledAttr}>";
                $options = $el['options'] ?? ($el['value'] ?? []);
                foreach ($options as $v => $l) {
                    $sel = (isset($el['selected']) && (string) $el['selected'] === (string) $v) ? 'selected' : '';
                    echo "<option value=\"{$v}\" {$sel}>{$l}</option>";
                }
                echo "</select>";
                if (isset($el['help'])) {
                    echo "<div class=\"form-text text-muted small mt-2\">{$el['help']}</div>";
                }
                echo "</div>";
                break;

            case 'checkbox':
                $name = $el['name'] ?? '';
                $id = $el['id'] ?? $name;
                $label = $el['label'] ?? '';
                $checked = !empty($el['checked']) ? 'checked' : '';
                $help = isset($el['help']) ? "<div class=\"form-text text-muted small mt-1\">{$el['help']}</div>" : '';
                $wrap = $el['wrapper_class'] ?? 'mb-3';

                // Initial state for 'enable' action
                $disabledAttr = '';
                if (isset($el['require']) && ($el['require_action'] ?? 'show') === 'enable') {
                    $disabledAttr = ' disabled';
                }

                echo "<div class=\"{$wrap}\" {$requireAttr}>
                        <div class=\"form-check form-switch\">
                            <input class=\"form-check-input\" type=\"checkbox\" name=\"{$name}\" id=\"{$id}\" value=\"on\" {$checked} {$disabledAttr}>
                            <label class=\"form-check-label fw-bold small\" for=\"{$id}\">{$label}</label>
                        </div>
                        {$help}
                      </div>";
                break;

            case 'media':
                $lbl = isset($el['label']) ? "<label class=\"form-label fw-bold text-dark small\">{$el['label']}</label>" : '';
                $name = $el['name'] ?? '';
                $val = htmlspecialchars((string) ($el['value'] ?? ''));
                $wrap = $el['wrapper_class'] ?? 'mb-3';
                $id = $el['id'] ?? 'media_' . str_replace(['[', ']'], '_', $name);
                
                echo "<div class=\"{$wrap}\" {$requireAttr}>
                        {$lbl}
                        <div class=\"media-drop-zone rounded-4 border-2 border-dashed bg-light p-3 position-relative text-center mb-1\" 
                             style=\"cursor: pointer; min-height: 120px;\" onclick=\"gxMediaSelector('{$id}')\">
                            <div id=\"{$id}_placeholder\" class=\"" . ($val ? 'd-none' : 'py-3') . "\">
                                <i class=\"bi bi-image fs-1 text-muted\"></i>
                                <p class=\"text-muted small mt-2 mb-0\">" . _("Click to select image") . "</p>
                            </div>
                            <img id=\"{$id}_preview\" class=\"img-fluid rounded-3 shadow-sm " . ($val ? '' : 'd-none') . "\" 
                                 src=\"{$val}\" style=\"max-height: 200px; width: 100%; object-fit: cover;\">
                            <input name=\"{$name}\" id=\"{$id}\" type=\"hidden\" value=\"{$val}\">
                        </div>
                        <div class=\"d-flex gap-2\">
                            <button type=\"button\" class=\"btn btn-xs btn-light border py-0 px-2 rounded-3\" onclick=\"gxMediaSelector('{$id}')\"><i class=\"bi bi-pencil small\"></i> Change</button>
                            <button type=\"button\" class=\"btn btn-xs btn-light border py-0 px-2 rounded-3 text-danger\" onclick=\"document.getElementById('{$id}').value=''; document.getElementById('{$id}_preview').classList.add('d-none'); document.getElementById('{$id}_placeholder').classList.remove('d-none');\"><i class=\"bi bi-trash small\"></i> Remove</button>
                        </div>
                      </div>";
                
                // Add script if not already added
                echo "<script>
                    if (typeof gxMediaSelector === 'undefined') {
                        window.gxMediaSelector = function(targetId) {
                            if (typeof GxMedia !== 'undefined') {
                                GxMedia.select(function (url) {
                                    document.getElementById(targetId).value = url;
                                    const preview = document.getElementById(targetId + '_preview');
                                    const placeholder = document.getElementById(targetId + '_placeholder');
                                    if (preview) { preview.src = url; preview.classList.remove('d-none'); }
                                    if (placeholder) { placeholder.classList.add('d-none'); }
                                });
                            } else {
                                const url = prompt('Enter Image URL:');
                                if (url) {
                                    document.getElementById(targetId).value = url;
                                    const preview = document.getElementById(targetId + '_preview');
                                    const placeholder = document.getElementById(targetId + '_placeholder');
                                    if (preview) { preview.src = url; preview.classList.remove('d-none'); }
                                    if (placeholder) { placeholder.classList.add('d-none'); }
                                }
                            }
                        };
                    }
                </script>";
                break;

            case 'repeater':
                $lbl = isset($el['label']) ? "<label class=\"form-label fw-bold text-dark mb-0\">{$el['label']}</label>" : '';
                $name = $el['name'] ?? '';
                $fields = $el['fields'] ?? [];
                $val = $el['value'] ?? [];
                
                // If value is a string, assume it's JSON from database
                if (!is_array($val) && !empty($val)) {
                    $val = json_decode(htmlspecialchars_decode((string)$val), true) ?: [];
                }
                
                $wrap = $el['wrapper_class'] ?? 'mb-4';
                $id = $el['id'] ?? 'repeater_' . str_replace(['[', ']'], '_', $name);
                
                echo "<div class=\"{$wrap} repeater-container\" {$requireAttr} id=\"{$id}\">
                        <div class=\"d-flex justify-content-between align-items-center mb-3\">
                            {$lbl}
                            <button type=\"button\" class=\"btn btn-primary btn-sm rounded-pill px-3 shadow-sm add-row-btn\">
                                <i class=\"bi bi-plus-circle me-1\"></i> Add New Point
                            </button>
                        </div>
                        <div class=\"repeater-rows space-y-3\">";
                
                $renderRow = function($rowData = [], $index = 'REPLACE_INDEX') use ($fields, $name) {
                    $rowHtml = "<div class=\"repeater-row card border shadow-none rounded-4 mb-3 position-relative\" data-index=\"{$index}\" style=\"overflow: visible !important;\">";
                    $rowHtml .= "  <div class=\"card-body p-3\">";
                    $rowHtml .= "    <div class=\"row g-3\">";
                    
                    foreach ($fields as $f) {
                        $fCopy = $f;
                        $fNameBase = $f['name'];
                        $fCopy['name'] = "{$name}[{$index}][{$fNameBase}]";
                        $fCopy['value'] = $rowData[$fNameBase] ?? ($f['default'] ?? '');
                        $fCopy['wrapper_class'] = $f['wrapper_class'] ?? 'col-md-12 mb-0';
                        $fCopy['boxclass'] = $f['boxclass'] ?? 'col-md-12';
                        
                        $rowHtml .= "<div class=\"{$fCopy['boxclass']}\">";
                        $rowHtml .= $this->renderElement($fCopy, true);
                        $rowHtml .= "</div>";
                    }
                    
                    $rowHtml .= "    </div>";
                    $rowHtml .= "    <button type=\"button\" class=\"btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 mt-n1 me-n1 shadow-sm remove-row-btn\" style=\"width:22px; height:22px; padding:0; display:flex; align-items:center; justify-content:center; border: 2px solid #fff; z-index: 10;\">
                                        <i class=\"bi bi-x fs-6\"></i>
                                    </button>";
                    $rowHtml .= "  </div>";
                    $rowHtml .= "</div>";
                    return $rowHtml;
                };

                if (!empty($val) && is_array($val)) {
                    foreach ($val as $idx => $row) {
                        echo $renderRow($row, $idx);
                    }
                }

                echo "  </div>"; // close repeater-rows
                
                // Template for JS - DO NOT htmlspecialchars here as it's inside <template>
                $template = $renderRow([], '[[INDEX]]');
                echo "  <template id=\"{$id}_template\">{$template}</template>";
                
                echo "</div>"; // close repeater-container

                echo "<style>
                    .repeater-row { transition: all 0.2s ease; border: 1px solid #eee !important; background: #fafafa !important; }
                    .repeater-row:hover { border-color: var(--bs-primary) !important; background: #fff !important; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.03); }
                    .mt-n2 { margin-top: -0.5rem !important; }
                    .me-n2 { margin-right: -0.5rem !important; }
                </style>";

                echo "<script>
                    (function() {
                        const initRepeater = function() {
                            const container = document.getElementById('{$id}');
                            if (!container || container.dataset.initialized) return;
                            container.dataset.initialized = 'true';

                            const rowsContainer = container.querySelector('.repeater-rows');
                            const addBtn = container.querySelector('.add-row-btn');
                            const templateEl = document.getElementById('{$id}_template');
                            if (!templateEl) return;
                            const template = templateEl.innerHTML;
                            let nextIndex = rowsContainer.querySelectorAll('.repeater-row').length;

                            addBtn.addEventListener('click', () => {
                                let rowHtml = template.replace(/\[\[INDEX\]\]/g, nextIndex);
                                const div = document.createElement('div');
                                div.innerHTML = rowHtml;
                                const newRow = div.firstChild;
                                rowsContainer.appendChild(newRow);
                                nextIndex++;
                                
                                // Re-init reactivity if needed
                                // if (window.initReactivity) window.initReactivity();
                            });

                            rowsContainer.addEventListener('click', (e) => {
                                const btn = e.target.closest('.remove-row-btn');
                                if (btn) {
                                    btn.closest('.repeater-row').remove();
                                }
                            });
                        };
                        
                        if (document.readyState === 'loading') {
                            document.addEventListener('DOMContentLoaded', initRepeater);
                        } else {
                            setTimeout(initRepeater, 100);
                        }
                    })();
                </script>";
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
                $val = htmlspecialchars((string) ($el['value'] ?? ''));
                $plc = htmlspecialchars((string) ($el['placeholder'] ?? 'Search...'));
                echo "<form method=\"get\" action=\"{$action}\" class=\"d-flex gap-2\">";
                if (isset($el['hidden'])) {
                    foreach ($el['hidden'] as $hn => $hv) {
                        echo "<input type=\"hidden\" name=\"" . htmlspecialchars($hn) . "\" value=\"" . htmlspecialchars($hv) . "\">";
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
                echo "<div class=\"pagination-wrapper\">{$html}</div>";
                break;

            case 'bulk_actions':
                $options = $el['options'] ?? [];
                $formAttr = isset($el['form']) ? "form=\"{$el['form']}\"" : "";
                echo "<div class=\"d-flex align-items-center gap-2\">
                        <select name=\"action\" class=\"form-select form-select-sm rounded-pill bg-light shadow-none border\" style=\"width: 160px;\" {$formAttr}>";
                foreach ($options as $v => $l) {
                    echo "<option value=\"{$v}\">" . _($l) . "</option>";
                }
                echo "  </select>
                        <button type=\"submit\" name=\"doaction\" class=\"btn btn-danger btn-sm rounded-pill px-4 shadow-sm fw-bold\" {$formAttr}>
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

            case 'recap_stats':
                $cls = $el['class'] ?? 'row text-center border-top py-4 mt-4 bg-light bg-opacity-25';
                echo "<div class=\"{$cls}\">";
                $items = $el['items'] ?? [];
                $count = count($items);
                foreach ($items as $i => $item) {
                    $color = $item['color'] ?? 'success';
                    $icon = $item['icon'] ?? 'bi bi-caret-up-fill';
                    $percent = $item['percent'] ?? '0%';
                    $val = $item['value'] ?? '0';
                    $lbl = $item['label'] ?? '';
                    $border = ($i < $count - 1) ? 'border-end' : '';

                    echo "
                    <div class=\"col-sm-3 col-6 {$border}\">
                        <div class=\"description-block pb-2\">
                            <span class=\"description-percentage text-{$color} mb-1 d-block small fw-bold\">
                                <i class=\"{$icon} me-1\"></i> {$percent}
                            </span>
                            <h4 class=\"description-header fw-black mb-1\" style=\"font-weight:900;\">{$val}</h4>
                            <span class=\"description-text text-muted text-uppercase extra-small tracking-widest\" style=\"font-size:0.6rem; letter-spacing:0.1em;\">{$lbl}</span>
                        </div>
                    </div>";
                }
                echo '</div>';
                break;

            case 'progress_group':
                $title = $el['title'] ?? '';
                echo "<div class=\"progress-group mb-4\">";
                if ($title) {
                    echo "<div class=\"fw-bold small text-muted text-uppercase mb-2\" style=\"font-size: 0.7rem; letter-spacing: 0.05em;\">{$title}</div>";
                }
                foreach ($el['items'] ?? [] as $item) {
                    $lbl = $item['label'] ?? '';
                    $val = $item['value'] ?? 0;
                    $max = $item['max'] ?? 100;
                    $color = $item['color'] ?? 'primary';
                    $pct = ($max > 0) ? ($val / $max) * 100 : 0;
                    echo "
                    <div class=\"mb-3\">
                        <div class=\"d-flex justify-content-between mb-1\">
                            <span class=\"small fw-bold\">{$lbl}</span>
                            <span class=\"small fw-black\"><b>{$val}</b>/{$max}</span>
                        </div>
                        <div class=\"progress rounded-pill shadow-none bg-light\" style=\"height: 8px;\">
                            <div class=\"progress-bar bg-{$color} rounded-pill\" role=\"progressbar\" style=\"width: {$pct}%\"></div>
                        </div>
                    </div>";
                }
                echo "</div>";
                break;

            case 'chart':
                $id = $el['id'] ?? 'chart-' . rand(100, 999);
                $height = $el['height'] ?? '300px';
                $chartType = $el['chart_type'] ?? 'line';
                $data = is_array($el['chart_data']) ? json_encode($el['chart_data']) : $el['chart_data'];

                // Enhanced default options based on chart type
                $defaultOptions = [
                    'responsive' => true,
                    'maintainAspectRatio' => false,
                    'plugins' => [
                        'legend' => ['display' => !in_array($chartType, ['bar', 'line'])], // Display legend for pie/doughnut
                        'tooltip' => ['enabled' => true]
                    ]
                ];

                if (in_array($chartType, ['line', 'bar'])) {
                    $defaultOptions['scales'] = [
                        'y' => [
                            'beginAtZero' => true,
                            'grid' => ['color' => 'rgba(0,0,0,0.05)', 'drawBorder' => false]
                        ],
                        'x' => [
                            'grid' => ['display' => false]
                        ]
                    ];
                }

                if ($chartType === 'line' && isset($el['area']) && $el['area']) {
                    // This logic is handled in the chart_data usually, but we can hint it
                }

                $options = json_encode(array_merge($defaultOptions, $el['chart_options'] ?? []));

                echo "<div class='chart-container mb-4' style='position: relative; height:{$height}; width:100%'>
                        <canvas id='{$id}'></canvas>
                      </div>";
                echo "<script>
                    (function() {
                        const initChart = function() {
                            if (typeof Chart === 'undefined') {
                                setTimeout(initChart, 200);
                                return;
                            }
                            const ctx = document.getElementById('{$id}');
                            if (!ctx) return;
                            new Chart(ctx, {
                                type: '{$chartType}',
                                data: {$data},
                                options: {$options}
                            });
                        };
                        if (typeof Chart === 'undefined' && !window.chartJsLoading) {
                            window.chartJsLoading = true;
                            const script = document.createElement('script');
                            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
                            script.onload = initChart;
                            document.head.appendChild(script);
                        } else {
                            setTimeout(initChart, 50);
                        }
                    })();
                </script>";
                break;

            case 'grid':
                $cls = $el['class'] ?? 'row g-4';
                echo "<div class=\"{$cls}\" {$requireAttr}>";
                foreach ($el['content'] ?? [] as $cel)
                    $this->renderElement($cel);
                echo '</div>';
                break;
            case 'column':
            case 'col':
                $cls = $el['class'] ?? 'col-md-12';
                echo "<div class=\"{$cls}\" {$requireAttr}>";
                foreach ($el['content'] ?? [] as $cel)
                    $this->renderElement($cel);
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