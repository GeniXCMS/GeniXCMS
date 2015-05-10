<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : MIT License
*    ------------------------------------------------------------
* filename : default.control.php
* version : 0.0.1 pre
* build : 20141006
*/
    $data['sitetitle'] = DASHBOARD;
    Theme::admin('header', $data);
    System::inc('dashboard', $data);
    //Mod::Options('moviedb');
    Theme::admin('footer');

/* End of file default.control.php */
/* Location: ./inc/lib/Control/Backend/default.control.php */
