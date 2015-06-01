<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20150312
* @version 0.0.3
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/

if (isset($_GET['view']) && $_GET['view'] == 'options') {
    $data['sitetitle'] = THEMES;
    Theme::admin('header', $data);
    Theme::options(Options::get('themes'));
    Theme::admin('footer');
}else{


    if (isset($_POST['upload'])) {
        if(!Token::isExist($_POST['token'])){
            $alertred[] = TOKEN_NOT_EXIST;
        }
        if (!isset($_FILES['theme']['name']) || $_FILES['theme']['name'] == "") {
            $alertred[] = NOFILE_UPLOADED;
        }

        if(!isset($alertred)){
            //Mod::activate($_GET['themes']);
            $path = "/inc/themes/";
            $allowed = array('zip');
            $theme = Upload::go('theme', $path, $allowed);
            //print_r($theme);
            $zip = new ZipArchive;
            if ($zip->open($theme['filepath']) === TRUE) {
                $zip->extractTo(GX_THEME);
                $zip->close();
                $data['alertgreen'][] = MSG_THEME_INSTALLED;
            } else {
                $data['alertred'][] = MSG_THEME_CANT_EXTRACT;
            }
            unlink($theme['filepath']);
        }else{
            $data['alertred'] = $alertred;
        }
        if(isset($_POST['token'])){ Token::remove($_POST['token']); }
    }

    if (isset($_GET['act'])) {

        if ($_GET['act'] == 'activate') {

            if(!Token::isExist($_GET['token'])){
                $alertred[] = TOKEN_NOT_EXIST;
            }

            if(!isset($alertred)){
                Theme::activate($_GET['themes']);
                $data['alertgreen'][] = THEME_ACTIVATED;
            }else{
                $data['alertred'] = $alertred;
            }
        }elseif ($_GET['act'] == 'remove') {
            if(!Token::isExist($_GET['token'])){
                $alertred[] = TOKEN_NOT_EXIST;
            }
            if (Theme::isActive($_GET['themes'])) {
                $alertred[] = MSG_THEME_IS_ACTIVE;
            }
            if(!isset($alertred)){
                if(Files::delTree(GX_THEME."/".$_GET['themes'])){
                    $data['alertgreen'][] = THEME_REMOVED;
                }else{
                    $data['alertred'][] = MSG_THEME_NOT_REMOVED;
                }
                
            }else{
                $data['alertred'] = $alertred;
            }
        }
        
    }

    $data['sitetitle'] = THEMES;
    $data['themes'] = Theme::thmList();
    Theme::admin('header', $data);
    System::inc('themes',$data);
    Theme::admin('footer');

}

/* End of file mods.control.php */
/* Location: ./inc/lib/Control/Backend/mods.control.php */