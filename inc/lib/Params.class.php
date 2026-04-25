<?php

/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * @since 1.1.0
 * @version 2.4.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class Params
{
    private static $_params;

    public function __construct()
    {
        $map = self::map();
        if (is_array(self::$_params)) {
            self::$_params = array_merge_recursive($map, self::$_params);
        } else {
            self::$_params = $map;
        }

        Hooks::attach('post_param_form_bottom', ['Params', 'renderBottom']);
        Hooks::attach('post_param_form_sidebar', ['Params', 'renderSidebar']);

        Hooks::attach('page_param_form_bottom', ['Params', 'renderBottom']);
        Hooks::attach('page_param_form_sidebar', ['Params', 'renderSidebar']);

        Hooks::attach('category_param_form', ['Params', 'renderCategory']);
        Hooks::attach('widget_param_form', ['Params', 'renderWidget']);

    }

    /**
     * Params Maps
     * structure :
     * $vars = [
     *  'sidebar' => [
     *    [
     *     'groupname' => '',
     *     'grouptitle' => '',
     *     'fields' => 
     *         [
     *           'title' => '',
     *           'label' => '',
     *           'name' => '',
     *           'type' => 'text' // text, textarea, dropdown, checkbox, etc
     *           'value' => '' // mixed str, arr
     *           'boxclass' => 'col-md-12' 
     *         ]
     *    ]
     *   ],
     *  'category' => []
     * ]
     */
    public static function map()
    {
        $params = [
            'sidebar' => [],
            'bottom' => [],
            'category' => [],
            'widget' => []
        ];

        return $params;
    }

    public static function register($vars = [])
    {
        if (!is_array(self::$_params)) {
            self::$_params = self::map();
        }
        $params = self::$_params;
        $res = array_merge_recursive($params, $vars);

        self::$_params = $res;
    }

    private static $_requirements = [];

    public static function build($vars)
    {
        global $data;
        $value = $vars['default'] ?? '';
        
        $inputName = $vars['name'];
        $vars['id'] = $vars['id'] ?? 'param_' . str_replace(['[', ']'], '_', $inputName);

        if (isset($data['post']) && is_array($data['post']) && isset($data['post'][0])) {
            $post = $data['post'][0];
            $id = is_object($post) ? $post->id : (isset($post['id']) ? $post['id'] : 0);
            if ($id > 0 && Posts::existParam($inputName, $id)) {
                $value = Posts::getParam($inputName, $id);
            }
        } elseif (isset($_GET['page']) && $_GET['page'] === 'categories' && isset($_GET['id'])) {
            $cat_id = Typo::int($_GET['id']);
            if ($cat_id > 0 && Categories::existParam($inputName, $cat_id)) {
                $value = Categories::getParam($inputName, $cat_id);
            }
        } elseif (isset($_GET['page']) && $_GET['page'] === 'widgets' && (isset($_GET['id']) || isset($_POST['id']))) {
            $widget_id = Typo::int($_GET['id'] ?? $_POST['id'] ?? 0);
            if ($widget_id > 0 && Widget::existParam($inputName, $widget_id)) {
                $value = Widget::getParam($inputName, $widget_id);
            }
        }

        // Handle Requirement
        if (isset($vars['require']) && $vars['require'] != '') {
            $requireId = 'param_' . str_replace(['[', ']'], '_', $vars['require']);
            $ajaxUrl = $vars['ajax_url'] ?? '';
            if ($ajaxUrl != '' && strpos($ajaxUrl, 'token=') === false) {
                $ajaxUrl .= (strpos($ajaxUrl, '?') === false ? '?' : '&') . 'token=' . TOKEN;
            }
            self::$_requirements[] = [
                'target' => $vars['id'],
                'require' => $requireId,
                'ajax_url' => $ajaxUrl
            ];
            
            // Pass requirement to UiBuilder for UI reactivity
            $vars['require'] = $requireId; 
        }

        $initialValue = $vars['value'] ?? null;
        $vars['name'] = "param[{$inputName}]";

        // Map Legacy types to UiBuilder types
        $type = $vars['type'] ?? 'text';
        if ($type == 'dropdown' || $type == 'select') {
            $vars['type'] = 'select';
            $vars['options'] = $initialValue;
            $vars['selected'] = $value;
            unset($vars['value']);
        } elseif (in_array($type, ['text', 'number', 'email', 'date', 'password', 'url', 'color', 'file', 'hidden'])) {
            $vars['type'] = 'input';
            $vars['input_type'] = $type;
            $vars['value'] = $value;
        } elseif ($type == 'checkbox') {
            $vars['value'] = $initialValue ?? 'on';
            if ($value == 'on' || $value == '1' || $value === true || $value === 'true') {
                $vars['checked'] = true;
            }
        } else {
            // General fallback
            $vars['value'] = $value;
        }

        // Map 'title' to 'label' so UiBuilder renders it natively
        if (isset($vars['title']) && !isset($vars['label'])) {
            $vars['label'] = _($vars['title']);
        }
        unset($vars['title']);

        $vars['class'] = $vars['class'] ?? 'form-control border-0 bg-light rounded-3 px-3 py-2 shadow-none';
        $vars['wrapper_class'] = 'mb-0';

        $ui = new UiBuilder();
        return $ui->renderElement($vars, true);
    }

    public static function renderBottom()
    {
        return self::render('bottom');
    }

    public static function renderSidebar()
    {
        return self::render('sidebar');
    }

    public static function renderCategory()
    {
        return self::render('category');
    }

    public static function renderWidget($type = null)
    {
        return self::render('widget', $type);
    }

    public static function render($location, $filter = null)
    {
        $html = "";
        $params = self::$_params;
        if (isset($params[$location]) && is_array($params[$location])) {
            foreach ($params[$location] as $k => $v) {
                // Check post_type if defined
                if (isset($v['post_type']) && $v['post_type'] != '') {
                    if ($location === 'category') {
                        $pt = $_GET['type'] ?? 'post';
                    } else {
                        $pt = $_GET['type'] ?? 'post';
                    }
                    if ($v['post_type'] != $pt) {
                        continue;
                    }
                }

                // Check widget_type filter
                if ($location === 'widget' && $filter !== null) {
                    if (isset($v['widget_type']) && $v['widget_type'] != $filter) {
                        continue;
                    }
                }

                $icon = isset($v['icon']) ? "<i class='{$v['icon']} me-2 text-primary'></i>" : "";
                
                if ($location === 'category') {
                    $html .= "<div class='mb-3'>";
                    if (isset($v['grouptitle']) && $v['grouptitle'] != '') {
                        $html .= "<label class='form-label text-muted text-uppercase fw-bold ls-1 fs-xs'>{$icon}{$v['grouptitle']}</label>";
                    }
                    $html .= "<div class='row g-3'>";
                } else {
                    $html .= "<div class='card border-0 shadow-sm rounded-4 mb-4'>
                    <div class='card-header bg-white border-0 py-3 px-4'>
                        <h6 class='fw-bold m-0'>{$icon}{$v['grouptitle']}</h6>
                    </div>
                        <div class='card-body px-4 pb-4 pt-0'>
                            <div class='container-fluid px-0'>
                                <div class='row g-3'>";
                }

                foreach ($v['fields'] as $k2 => $v2) {
                    // Check post_type at field level if defined
                    if (isset($v2['post_type']) && $v2['post_type'] != '') {
                        $pt = $_GET['type'] ?? 'post';
                        if ($v2['post_type'] != $pt) {
                            continue;
                        }
                    }

                    $boxClass = $v2['boxclass'] ?? 'col-md-12';
                    $html .= "<div class='{$boxClass}'>";
                    $html .= self::build($v2);
                    $html .= "</div>";
                }

                if ($location === 'category') {
                    $html .= "</div></div>";
                } else {
                    $html .= "</div></div></div></div>";
                }
            }
        }

        if (!empty(self::$_requirements)) {
            $html .= "<script>";
            foreach (self::$_requirements as $req) {
                $targetId = $req['target'];
                $requireId = $req['require'];
                $ajaxUrl = $req['ajax_url'];
                
                $html .= "
                document.addEventListener('DOMContentLoaded', function() {
                    const targetEl = document.getElementById('{$targetId}');
                    const requireEl = document.getElementById('{$requireId}');
                    
                    if (targetEl && requireEl) {
                        const updateTarget = function(isInitial = false) {
                            const val = requireEl.value;
                            if (val && '{$ajaxUrl}' !== '') {
                                fetch('{$ajaxUrl}&require_val=' + encodeURIComponent(val))
                                    .then(response => response.json())
                                    .then(data => {
                                        const currentValue = targetEl.value;
                                        targetEl.innerHTML = '';
                                        for (const [v, label] of Object.entries(data)) {
                                            const opt = document.createElement('option');
                                            opt.value = v;
                                            opt.textContent = label;
                                            if (isInitial && v === currentValue) opt.selected = true;
                                            targetEl.appendChild(opt);
                                        }
                                        targetEl.disabled = false;
                                    });
                            } else {
                                targetEl.innerHTML = '<option value=\"\">-- Select --</option>';
                                targetEl.disabled = true;
                            }
                        };
                        requireEl.addEventListener('change', () => updateTarget(false));
                        // Initial check
                        if (requireEl.value) {
                            updateTarget(true);
                        } else {
                            targetEl.disabled = true;
                        }
                    }
                });";
            }
            $html .= "</script>";
            self::$_requirements = [];
        }

        return $html;
    }

}