<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

class ModControl extends BaseControl
{
    public function run($param)
    {
        $route = Router::scrap($param);
        $mod_raw = (SMART_URL) ? $route['mod'] : Typo::cleanX($_GET['mod']);

        $mod_parts = explode('/', $mod_raw);
        $mod_name = $mod_parts[0];
        
        // If the first part is NOT a valid module folder, use 'mod' as default
        if (!Mod::exist($mod_name)) {
            $mod_name = 'mod';
            $mod_params = $mod_parts; 
        } else {
            array_shift($mod_parts); // Remove mod name, keep params
            $mod_params = $mod_parts;
        }

        $data['mod'] = $mod_name;
        $data['mod_params'] = $mod_params;
        $data['p_type'] = 'mod';
        $data['max'] = Options::v('post_perpage');

        $data['recent_posts'] = Posts::lists([
            'num' => 5,
            'image' => true,
            'image_size' => 100,
            'title' => true,
            'date' => true,
            'type' => "post",
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

        if (Mod::exist($mod_name)) {
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
