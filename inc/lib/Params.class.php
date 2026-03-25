<?php


class Params
{
    private static $_params;

    public function __construct()
    {
        self::$_params = self::map();

        Hooks::attach('post_param_form_bottom', ['Params', 'renderBottom']);
        Hooks::attach('post_param_form_sidebar', ['Params', 'renderSidebar']);

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

    public static function register($vars=[])
    {
        $params = self::$_params;
        $res = array_merge($params, $vars);

        self::$_params = $res;
    }

    public static function build($vars)
    {
        global $data;
        // print_r($data);
        if(is_array($vars)) {
            $value = '';
            if( isset($data['post']) && Posts::existParam($vars['name'], $data['post'][0]->id )) {
                $value = Posts::getParam($vars['name'], $data['post'][0]->id);
            }
            // check type 
            if( $vars['type'] == 'text') {
                return "<input type='text' name='param[{$vars['name']}]' value='{$value}' class='form-control'>";
            }
            if( $vars['type'] == 'checkbox') {
                return "<input type='checkbox' name='param[{$vars['name']}]' value='{$value}' class=''>";
            }
            if( $vars['type'] == 'textarea') {
                return "<textarea name='param[{$vars['name']}]' class='form-control'>{$value}</textarea>";
            }
            if( $vars['type'] == 'dropdown') {
                $html = "<select name='param[{$vars['name']}]' class='form-control'>";
                foreach($vars['value'] as $k => $v ) {
                    $sel = $value == $v ? "selected": "";
                    $html .= "<option value='{$v}' {$sel}>".ucfirst($v)."</option>";
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
        if( is_array($params[$location]) ) {
            foreach( $params[$location] as $k => $v ) {
                $html .= "<div class='card card-outline card-secondary mt-3'>
                <div class='card-header'>{$v['grouptitle']}</div>
                    <div class='class-body'><div class='container-fluid'><div class='row'>";
                foreach( $v['fields'] as $k2 => $v2 ) {
                    $html .= "<div class='{$v2['boxclass']}'><div class=\"form-group\">
                        <label for=\"{$v2['name']}\">"._($v2["title"])."</label> ";
                    $html .= self::build($v2);    
                    $html .= "</div></div>";
                }
                $html .= "</div></div></div></div>";
            }
        }

        echo $html;
    }

}
