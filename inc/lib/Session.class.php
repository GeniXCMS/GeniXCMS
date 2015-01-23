<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : Private
*    ------------------------------------------------------------
* filename : Session.class.php
* version : 0.0.1 pre
* build : 20140925
*/

class Session
{

    public function __construct () {

    }

    public static function start() {
        session_name('GeniXCMS');
        session_start();
        //unset($_SESSION);
        if (!isset($_SESSION['gxsess']) || $_SESSION['gxsess'] == "" ) {
            $_SESSION['gxsess'] = array (
                                    'key' => self::sesKey(),
                                    'time' => date("Y-m-d H:i:s"),
                                    'val' => array()
                                );
        }

        $GLOBALS['start_time'] = microtime(TRUE);

    }

    private static function sesKey() {
        $ip = $_SERVER['REMOTE_ADDR'];
        $browser = $_SERVER['HTTP_USER_AGENT'];
        $dt = date("Y-m-d H");

        $key = md5($ip.$browser.$dt);
        return $key;
    }

    /*   
    *    Session Handler 
    *
    *    $gxsess = array (
    *                    'key' => 'sesskey_val',
    *                    'time' => 'sesstime_val',
    *                    'val' => array (
    *                                   'sessval1_key' => 'sessval1_val',
    *                                   'sessval2_key' => 'sessval2_val',
    *                                 )
    *                )
    */
    public static function get_session($vars) {
        
    }

    public static function val ($vars) {
        $val = $_SESSION['gxsess']['val'];
        foreach ($val as $k => $v) {
            # code...
            switch ($k) {
                case $vars:
                    return $v;
                    break;
                
                default:
                    return false;
                    break;
            }
        }
    }

    public static function set_session($vars) {
        if (is_array($vars)) {
            if(is_array($_SESSION['gxsess']['val'])){
                $arr = array_merge($_SESSION['gxsess']['val'], $vars);
                $_SESSION['gxsess']['val'] = $arr;
            }else{
                $_SESSION['gxsess']['val'] = $vars;
            }

           

        }


    }

    public static function destroy () {
        session_destroy();
        unset($_SESSION['gxsess']);
    }

}


/* End of file system.class.php */
/* Location: ./inc/lib/system.class.php */