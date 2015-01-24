<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : MIT License
*    ------------------------------------------------------------
* filename : GxMain.class.php
* version : 0.0.1 pre
* build : 20140925
*/

class GxMain 
{

    public function __construct() {
        global $u, $sys, $db;
        $sys = new System();
        $sess = new Session();
        $db = new Db();
        $u = new User();
        $thm = new Theme();
        
        
    }


    public function index() {
        global $u, $sys, $db;
        Session::start();
        System::gZip();
        Control::handler('frontend');
        System::Zipped();
    }

    public function admin () {
        global $u, $sys, $db, $sess;
        Session::start();
        $u->secure(0);
        System::gZip();
        Theme::admin('header');
        Control::handler('backend');
        //Session::destroy();
        Theme::admin('footer');
        System::Zipped();

    }

    
}


/* End of file GxMain.class.php */
/* Location: ./inc/lib/GxMain.class.php */