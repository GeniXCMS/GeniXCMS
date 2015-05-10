<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20140925
* @version 0.0.3
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/

/**
* GxMain Class
*
* This class is the main class for call all the necessary controller.
* 
* 
* @author Puguh Wijayanto (www.metalgenix.com)
* @since 0.0.1
*/
class GxMain 
{

    /**
    * GxMain Initiation Function.
    * This will check the config file at inc/config/config.php exist or not
    * if not exist, run the installation proccess.
    * 
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1
    */
    public function __construct() {
        if (System::existConf()) {
            new System();
            new Site();
            Vendor::autoload();
            Token::create();
            Mod::loader();
            Theme::loader();
        }else{
            $this->install();
        }
    }

    /**
    * GxMain Index Function.
    * This will load the frontpage controller.
    * 
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1
    */
    public function index() {
        Session::start();
        System::gZip();
        Control::handler('frontend');
        System::Zipped();
    }

    /**
    * GxMain Admin Function.
    * This will load the backend controller. Secured, so to access it must be 
    * logged in with a current privilege. Default privilege is 2.
    * 
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1
    */
    public function admin () {
        Session::start();
        User::secure();
        System::gZip();
        if( User::access(2) ) {
            Control::handler('backend');
        }else{
            Theme::admin('header');
            Control::error('noaccess');
            Theme::admin('footer');
        }
        System::Zipped();
    }

    /**
    * GxMain Install Function.
    * This will load the install controller.
    * 
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1
    */
    public function install () {
        Session::start();
        System::gZip();
        Theme::install('header');
        Control::handler('install');
        Theme::install('footer');
        System::Zipped();
    }

    
}


/* End of file GxMain.class.php */
/* Location: ./inc/lib/GxMain.class.php */