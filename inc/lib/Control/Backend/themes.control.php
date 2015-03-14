<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20150312
* @version 0.0.2
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/

if (isset($_POST['upload'])) {
    if(!Token::isExist($_POST['token'])){
        $alertred[] = TOKEN_NOT_EXIST;
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
            $data['alertgreen'][] = "Themes Installed Sucesfully.";
        } else {
            $data['alertred'][] = "Can't extract files.";
        }
        unlink($theme['filepath']);
    }else{
        $data['alertred'] = $alertred;
    }
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
            $alertred[] = "Theme is Active. Please deactivate first.";
        }
        if(!isset($alertred)){
            if(Files::delTree(GX_THEME."/".$_GET['themes'])){
                $data['alertgreen'][] = THEME_REMOVED;
            }else{
                $data['alertred'][] = "Theme Cannot removed. Please check if You had permission to remove the files.";
            }
            
        }else{
            $data['alertred'] = $alertred;
        }
    }
    
}


$data['themes'] = Theme::thmList();
System::inc('themes',$data);


/* End of file mods.control.php */
/* Location: ./inc/lib/Control/Backend/mods.control.php */