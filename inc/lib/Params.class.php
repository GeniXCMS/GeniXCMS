<?php

/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * @since 1.1.0
 * @version 2.1.1
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
        self::$_params = self::map();

        Hooks::attach('post_param_form_bottom', ['Params', 'renderBottom']);
        Hooks::attach('post_param_form_sidebar', ['Params', 'renderSidebar']);

        Hooks::attach('page_param_form_bottom', ['Params', 'renderBottom']);
        Hooks::attach('page_param_form_sidebar', ['Params', 'renderSidebar']);

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
     *   ]
     * ]
     */
    public static function map()
    {
        $params = [
            'sidebar' => [],
            'bottom' => []
        ];

        return $params;
    }

    public static function register($vars = [])
    {
        $params = self::$_params;
        $res = array_merge_recursive($params, $vars);

        self::$_params = $res;
    }

    public static function build($vars)
    {
        global $data;
        // print_r($data);
        $html = '';
        if (is_array($vars)) {
            $value = '';
            if (isset($data['post']) && is_array($data['post']) && isset($data['post'][0])) {
                $post = $data['post'][0];
                $id = is_object($post) ? $post->id : (isset($post['id']) ? $post['id'] : 0);
                if ($id > 0 && Posts::existParam($vars['name'], $id)) {
                    $value = Posts::getParam($vars['name'], $id);
                }
            }
            // check type 
            if ($vars['type'] == 'text') {
                return "<input type='text' name='param[{$vars['name']}]' value='{$value}' class='form-control border-0 bg-light rounded-3 px-3 py-2 fw-medium shadow-none'>";
            }
            if ($vars['type'] == 'checkbox') {
                $checked = $value == 'on' ? "checked" : "";
                return "<div class='form-check form-switch'>
                    <input type='checkbox' name='param[{$vars['name']}]' class='form-check-input' role='switch' {$checked}>
                </div>";
            }
            if ($vars['type'] == 'textarea') {
                return "<textarea name='param[{$vars['name']}]' class='form-control border-0 bg-light rounded-3 px-3 py-2 fw-medium shadow-none' rows='4'>{$value}</textarea>";
            }
            if ($vars['type'] == 'dropdown') {
                $html = "<select name='param[{$vars['name']}]' class='form-select border-0 bg-light rounded-3 px-3 py-2 fw-medium shadow-none'>";
                foreach ($vars['value'] as $k => $v) {
                    $sel = $value == $v ? "selected" : "";
                    $html .= "<option value='{$v}' {$sel}>" . ucfirst($v) . "</option>";
                }
                $html .= "</select>";
            }
        }

        return $html;

    }

    public static function renderBottom()
    {
        return self::render('bottom');
    }

    public static function renderSidebar()
    {
        return self::render('sidebar');
    }

    public static function render($location)
    {
        $html = "";
        $params = self::$_params;
        if (isset($params[$location]) && is_array($params[$location])) {
            foreach ($params[$location] as $k => $v) {
                // Check post_type if defined
                if (isset($v['post_type']) && $v['post_type'] != '') {
                    $pt = $_GET['type'] ?? 'post';
                    if ($v['post_type'] != $pt) {
                        continue;
                    }
                }

                $icon = isset($v['icon']) ? "<i class='{$v['icon']} me-2 text-primary'></i>" : "";
                $html .= "<div class='card border-0 shadow-sm rounded-4 mb-4 overflow-hidden'>
                <div class='card-header bg-white border-0 py-3 px-4'>
                    <h6 class='fw-bold m-0'>{$icon}{$v['grouptitle']}</h6>
                </div>
                    <div class='card-body px-4 pb-4 pt-0'>
                        <div class='container-fluid px-0'>
                            <div class='row g-3'>";
                foreach ($v['fields'] as $k2 => $v2) {
                    // Check post_type at field level if defined
                    if (isset($v2['post_type']) && $v2['post_type'] != '') {
                        $pt = $_GET['type'] ?? 'post';
                        if ($v2['post_type'] != $pt) {
                            continue;
                        }
                    }

                    $boxClass = $v2['boxclass'] ?? 'col-md-12';
                    $html .= "<div class='{$boxClass}'>
                        <div class='mb-2'>
                            <label class='form-label fw-bold text-dark small text-uppercase opacity-75' style='font-size: 0.7rem; letter-spacing: 0.5px;'> " . _($v2["title"]) . "</label> ";
                    $html .= self::build($v2);
                    $html .= "</div></div>";
                }
                $html .= "</div></div></div></div>";
            }
        }

        echo $html;
    }

}
