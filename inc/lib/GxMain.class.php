<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 0.0.1 build date 20140925
 * @version 2.3.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

/**
 * GxMain Class.
 *
 * This class is the main class for call all the necessary controller.
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
     * @since 0.0.1
     */
    public function __construct()
    {
        if (System::existConf()) {
            new System();
        } else {
            $this->install();
            exit;
        }
    }

    /**
     * GxMain Index Function.
     *
     * This will load the frontpage controller.
     *
     * @since 0.0.1
     */
    public function index()
    {
        // echo "Frontend";
        Control::frontend();
    }

    /**
     * GxMain Admin Function.
     *
     * This will load the backend controller. Secured, so to access it must be
     * logged in with a current privilege. Default privilege is 2.
     *
     * @since 0.0.1
     */
    public function admin()
    {
        User::secure();
        // System::gZip();
        if (User::access(4)) {
            // System::versionCheck();
            Control::handler('backend');
        } else {
            Theme::admin('headermini');
            Control::error('noaccess');
            Theme::admin('footermini');
        }
        // System::Zipped();
    }

    /**
     * GxMain Install Function.
     *
     * This will load the install controller.
     *
     * @since 0.0.1
     */
    public function install()
    {
        Session::start();
        // System::gZip();
        Theme::install('header');
        Control::handler('install');
        Theme::install('footer');
        // System::Zipped();
    }
}

/* End of file GxMain.class.php */
/* Location: ./inc/lib/GxMain.class.php */
