<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * 
 * @since 0.0.1
 * @version 2.3.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class ModControl extends BaseControl
{
    public function run($param)
    {
        $route = Router::scrap($param);
        $mod_raw = isset($route['mod']) ? $route['mod'] : (isset($_GET['mod']) ? Typo::cleanX($_GET['mod']) : '');
        $mod_raw = (string) $mod_raw; // Ensure it is a string

        $mod_parts = explode('/', $mod_raw);

        // Use the very first part as the module hook identifier (e.g. 'contactPage')
        $mod_name = $mod_parts[0];
        array_shift($mod_parts); // Pluck it out
        $mod_params = $mod_parts; // The rest are sub-params

        $data['mod'] = $mod_name;
        $data['mod_params'] = $mod_params;
        $data = array_merge($route, $data);
        $data['p_type'] = 'mod';
        $data['max'] = Options::v('post_perpage');

        // Detect post_type from module properties if registered
        $post_type = Mod::getProperty($mod_name, 'post_type') ?: "post";
        $data['post_type'] = $post_type;

        $data['recent_posts'] = Posts::lists([
            'num' => 5,
            'image' => true,
            'image_size' => 100,
            'title' => true,
            'date' => true,
            'type' => $post_type,
            'class' => [
                'row' => 'd-flex align-items-center mb-3 border-bottom pb-3',
                'img' => 'rounded flex-shrink-0',
                'list' => 'flex-grow-1 ms-3',
                'h4' => 'fs-5 mb-0 text-dark',
                'date' => 'text-body-secondary mt-0'
            ]
        ]);

        $data['sitetitle'] = Mod::getTitle($mod_name);
        if ($data['sitetitle'] == "" && isset($mod_params[0])) {
            $data['sitetitle'] = Mod::getTitle($mod_params[0]);
        }
        $data['title'] = $data['sitetitle'];

        // Modules self-register their hook IDs via Mod::addMenuList() at init.
        // Check against that list — not the physical folder — for correct routing.
        if (array_key_exists($mod_name, Mod::$listMenu)) {
            $this->render('mod', $data);
            exit();
        } else {
            Control::error('404');
            exit();
        }
    }
}

$control = new ModControl();
$control->run($param);
