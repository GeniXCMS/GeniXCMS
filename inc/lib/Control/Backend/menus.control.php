<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20141007
* @version 0.0.1
* @link https://github.com/semplon/GeniXCMS
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/

if(isset($_GET['act'])) { $act = $_GET['act'];}else{$act = '';}
switch ($act) {
    case 'add':
        # code...
        if (isset($_POST['submit'])) {
            # code...
            $submit = true;
        }else{
            $submit = false;
        }
        switch ($submit) {
            case true:
                # code...
                if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
                    // VALIDATE ALL
                    $alertred[] = "Token not exist, or your time has expired. Please refresh your browser to get a new token.";
                }
                if(isset($alertred)){
                    $data['alertred'] = $alertred;
                }else{
                    $menus = Options::get('menus');
                    $menus = json_decode(Options::get('menus'), true);
                   //echo "<pre>"; print_r($menus); echo "</pre>";
                    // $menu = array(
                    //                 $_POST['id']  =>  array(
                    //                             'name' => $menus[$_POST['id']]['name'],
                    //                             'class' => $menus[$_POST['id']]['class'],
                    //                             'menu' => array(
                    //                                     'parent' => $_POST['parent'],
                    //                                     'menuid' => $_POST['id'],
                    //                                     'type' => $_POST['type'],
                    //                                     'value' => $_POST[$_POST['type']]
                    //                                 )
                    //                         )
                    //                 );
                    
                    // if(is_array($menus)){
                    //     $menu = array_merge($menus, $menu);
                    // }
                    // echo "<pre>"; print_r($menu); echo "</pre>";
                    //$menu = $menus;
                    $menu[$_POST['id']]['menu'] = $menus[$_POST['id']]['menu'];
                    $menu[$_POST['id']]['menu'][] = array(
                                                        'parent' => $_POST['parent'],
                                                        'menuid' => $_POST['id'],
                                                        'name' => Typo::cleanX($_POST['name']),
                                                        'type' => $_POST['type'],
                                                        'value' => $_POST[$_POST['type']],
                                                        'sub' => ''
                                                    );
                     $menu = array(
                                    $_POST['id']  =>  array(
                                                'name' => $menus[$_POST['id']]['name'],
                                                'class' => $menus[$_POST['id']]['class'],
                                                'menu' => $menu[$_POST['id']]['menu']    
                                            )
                                    );
                    if(is_array($menus)){
                        $menu = array_merge($menus, $menu);
                    }
                    //echo "<pre>"; print_r($menu); echo "</pre>";
                    $menu = json_encode($menu);
                    //echo "<pre>"; print_r($menu); echo "</pre>";
                    //Options::update('menus', $menu);

                    $vars = array(
                                'parent' => $_POST['parent'],
                                'menuid' => $_POST['id'],
                                'name' => Typo::cleanX($_POST['name']),
                                'class' => Typo::cleanX($_POST['class']),
                                'type' => $_POST['type'],
                                'value' => $_POST[$_POST['type']]
                            );
                    Menus::insert($vars);
                    $data['alertgreen'][] = 'Menu Added';
                }
                break;
            
            default:
                # code...

                break;
        }
        //$data['abc'] = "abc";
        if(isset($_GET['id'])){
            $menuid = $_GET['id'];
        }else{
            $menuid = '';
        }
        $data['parent'] = Menus::isHadParent('', $menuid);
        //echo "<pre>"; print_r($data); echo "</pre>";
        System::inc('menus_form', $data);
        break;

    case 'edit':
        # code...
        if (isset($_POST['edititem'])) {
            # code...
            $submit = true;
        }else{
            $submit = false;
        }
        switch ($submit) {
            case true:
                if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
                    // VALIDATE ALL
                    $alertred[] = "Token not exist, or your time has expired. Please refresh your browser to get a new token.";
                }
                if(isset($alertred)){
                    $data['alertred'] = $alertred;
                }else{

                    $vars = array(
                                'parent' => $_POST['parent'],
                                'menuid' => $_POST['id'],
                                'name' => Typo::cleanX($_POST['name']),
                                'class' => Typo::cleanX($_POST['class']),
                                'type' => $_POST['type'],
                                'value' => $_POST[$_POST['type']]
                            );
                    $vars = array(
                                'id' => $_GET['itemid'],
                                'key' => $vars
                            );
                    Menus::update($vars);
                    $data['alertgreen'][] = 'Menu Updated';
                }
                break;
            
            default:
                # code...

                break;
        }

        if(isset($_GET['id'])){
            $menuid = $_GET['id'];
        }else{
            $menuid = '';
        }
        $data['menus'] = Menus::getId($_GET['itemid']);
        $data['parent'] = Menus::isHadParent('', $menuid);
        System::inc('menus_form_edit', $data);
        break;
    case 'del':
        if(isset($_GET['itemid'])){
            if (!isset($_GET['token']) || !Token::isExist($_GET['token'])) {
                // VALIDATE ALL
                $alertred[] = "Token not exist, or your time has expired. Please refresh your browser to get a new token.";
            }
            if(isset($alertred)){
                $data['alertred'] = $alertred;
            }else{
                Menus::delete($_GET['itemid']);
                $data['alertgreen'][] = 'Menu Deleted';
            }
        }else{
            $data['alertred'][] = 'No ID Selected.';
        }
        break;
    default:
        # code...
        if (isset($_POST['submit'])) {
            # code...
            $submit = true;
        }else{
            $submit = false;
        }
        switch ($submit) {
            case true:
                # code...
                if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
                    // VALIDATE ALL
                    $alertred[] = "Token not exist, or your time has expired. Please refresh your browser to get a new token.";
                }
                if(isset($alertred)){
                    $data['alertred'] = $alertred;
                }else{
                    $menu = array(
                                    $_POST['id']  =>  array(
                                                'name' => Typo::cleanX($_POST['name']),
                                                'class' => Typo::cleanX($_POST['class']),
                                                'menu' => array()
                                            )
                                    );
                    $menus = json_decode(Options::get('menus'), true);
                    if(is_array($menus)){
                        $menu = array_merge($menus, $menu);
                    }
                    
                    $menu = json_encode($menu);
                    Options::update('menus', $menu);
                }
                break;
            
            default:
                # code...
                
                break;
        }


        // ADD MENU ITEM START
        
        if (isset($_POST['additem'])) {
            # code...
            $submit = true;
        }else{
            $submit = false;
        }
        switch ($submit) {
            case true:
                
                if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
                    // VALIDATE ALL
                    $alertred[] = "Token not exist, or your time has expired. Please refresh your browser to get a new token.";
                }
                if(isset($alertred)){
                    $data['alertred'] = $alertred;
                }else{
                    $vars = array(
                                'parent' => $_POST['parent'],
                                'menuid' => $_POST['id'],
                                'name' => Typo::cleanX($_POST['name']),
                                'class' => Typo::cleanX($_POST['class']),
                                'type' => $_POST['type'],
                                'value' => $_POST[$_POST['type']]
                            );
                    Menus::insert($vars);
                    $data['alertgreen'][] = 'Menu Item Added';
                }
                break;
            
            default:
                # code...

                break;
        }

        // ADD MENU ITEM END


        // CHANGE ORDER START
        if(isset($_POST['changeorder'])){
            $submit = true;
        }else{
            $submit = false;
        }
        switch ($submit) {
            case true:
                # code...
                // echo "<pre>";
                // print_r($_POST['order']);
                // echo "</pre>";
                if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
                    // VALIDATE ALL
                    $alertred[] = "Token not exist, or your time has expired. Please refresh your browser to get a new token.";
                }
                if(isset($alertred)){
                    $data['alertred'] = $alertred;
                }else{
                    Menus::updateMenuOrder($_POST['order']);
                    $data['alertgreen'][] = 'Menu Order Changed';
                }
                break;
            
            default:
                # code...
                break;
        }

        // CHANGE ORDER END

        $data['menus'] = Options::get('menus');
        System::inc('menus', $data);
        break;
}
    


/* End of file menus.control.php */
/* Location: ./inc/lib/Control/Backend/menus.control.php */