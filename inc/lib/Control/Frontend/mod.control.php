<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

class ModControl extends BaseControl
{
    public function run($param)
    {
        $route = Router::scrap($param);
        $data['mod'] = (SMART_URL) ? $route['mod'] : Typo::cleanX($_GET['mod']);
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

        $data['sitetitle'] = Mod::getTitle($data['mod']);

        if (Hooks::exist($data['mod'], 'mod_control')) {
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
