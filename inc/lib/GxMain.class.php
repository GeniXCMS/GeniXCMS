<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20140925
 *
 * @version 1.1.11
 *
 * @link https://github.com/semplon/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @copyright 2014-2020 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

/**
 * GxMain Class.
 *
 * This class is the main class for call all the necessary controller.
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 *
 * @since 0.0.1
 */
class GxMain
{
    /**
     * GxMain Initiation Function.
     *
     * This will check the config file at inc/config/config.php exist or not
     * if not exist, run the installation proccess.
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     *
     * @since 0.0.1
     */
    public function __construct()
    {
        if (System::existConf()) {
            new System();
        } else {
            $this->install();
        }
    }

    /**
     * GxMain Index Function.
     *
     * This will load the frontpage controller.
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     *
     * @since 0.0.1
     */
    public function index()
    {
        Control::frontend();
    }

    /**
     * GxMain Admin Function.
     *
     * This will load the backend controller. Secured, so to access it must be
     * logged in with a current privilege. Default privilege is 2.
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     *
     * @since 0.0.1
     */
    public function admin()
    {
        User::secure();
        System::gZip();
        if (User::access(2)) {
            // System::versionCheck();
            Control::handler('backend');
        } else {
            Theme::admin('header');
            Control::error('noaccess');
            Theme::admin('footer');
        }
        System::Zipped();
    }

    /**
     * GxMain Install Function.
     *
     * This will load the install controller.
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     *
     * @since 0.0.1
     */
    public function install()
    {
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
