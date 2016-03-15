<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
*
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.8 build date 20160313
* @version 0.0.8
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2016 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/


$data['sitetitle'] = "Permalink";


if (isset($_POST['change'])) {
    if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
        $alertred[] = TOKEN_NOT_EXIST;
    }

    if (!isset($alertred)) {
        $vars = array();
        $flip = array_flip($_POST);
        // print_r($_POST);
        $sql = "SELECT * FROM `options` WHERE `value` = 'on'";
        $q = Db::result($sql);
        $input = array('permalink_use_index_php');

        foreach($q as $ob) {

            if( in_array($ob->name, $input ) ) {

                if( isset( $flip[$ob->name] ) ) {

                    $vars[$ob->name] = 'on';

                }else{

                    $vars[$ob->name] = 'off';

                }

            }

        }

        unset($_POST['token']);
        unset($_POST['change']);
        // print_r($vars);
        foreach ($_POST as $key => $val) {
            # code...
            $vars[$key] = Typo::cleanX($val);
        }
        // print_r($vars);


        Options::update($vars);
        new Options();
    }else{
        $data['alertred'] = $alertred;
    }
}

$data['permalink_use_index_php'] = Options::v('permalink_use_index_php');

Theme::admin('header', $data);
System::inc('permalink', $data);
Theme::admin('footer');

/* End of file default.control.php */
/* Location: ./inc/lib/Control/Backend/default.control.php */
