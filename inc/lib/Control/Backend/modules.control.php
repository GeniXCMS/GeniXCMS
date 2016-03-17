<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20150312
* @version 0.0.8
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2016 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/
$data['sitetitle'] = MODULES;
if(isset($GLOBALS['alertDanger']))
    $data['alertDanger'] = $GLOBALS['alertDanger'];
if(isset($GLOBALS['alertSuccess']))
    $data['alertSuccess'][] = $GLOBALS['alertSuccess'];

if (isset($_POST['upload'])) {
    if(!Token::isExist($_POST['token'])){
        $alertDanger[] = TOKEN_NOT_EXIST;
    }
    if (!isset($_FILES['module']['name']) || $_FILES['module']['name'] == "") {
        $alertDanger[] = NOFILE_UPLOADED;
    }

    if(!isset($alertDanger)){
        //Mod::activate($_GET['modules']);
        $path = "/inc/mod/";
        $allowed = array('zip');
        $mod = Upload::go('module', $path, $allowed);
        //print_r($mod);
        $zip = new ZipArchive;
        if ($zip->open($mod['filepath']) === TRUE) {
            $zip->extractTo(GX_MOD);
            $zip->close();
            Hooks::run('module_install_action', $mod);
            $data['alertSuccess'][] = MSG_MOD_INSTALLED;
        } else {
            $data['alertDanger'][] = MSG_MOD_CANT_EXTRACT;
        }
        unlink($mod['filepath']);
    }else{
        $data['alertDanger'] = $alertDanger;
    }
    if(isset($_POST['token'])){ Token::remove($_POST['token']); }
}



$data['mods'] = Mod::modList();
Theme::admin('header', $data);
System::inc('modules',$data);
Theme::admin('footer');

/* End of file mods.control.php */
/* Location: ./inc/lib/Control/Backend/mods.control.php */