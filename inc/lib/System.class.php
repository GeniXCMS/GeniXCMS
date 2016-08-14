<?php

if (defined('GX_LIB') === false) {
    die('Direct Access Not Allowed!');
}
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20140925
 *
 * @version 1.0.0
 *
 * @link https://github.com/semplon/GeniXCMS
 * @link http://genixcms.org
 *
 * @author Puguh Wijayanto <psw@metalgenix.com>
 * @copyright 2014-2016 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class System
{
    /**
     * GeniXCMS Version Variable.
     *
     * @return float
     */
    public static $version = '1.0.0';

    /**
     * GeniXCMS Version Release.
     *
     * @return string
     */
    public static $v_release = '';

    /**
     * System Constructor.
     * Initializing the system, check the config file.
     *
     * @author Puguh Wijayanto <psw@metalgenix.com>
     *
     * @since 0.0.1
     */
    public function __construct()
    {

        /* Load config file */
        self::config('config');

        /* Initiate database */
        new Db();

        /* Initiate Hooks system */
        new Hooks();

        /* Initiate Options variables. */
        new Options();

        /* Initate Token creation */
        new Token();

        /* Initate Date localization */
        new Date();

        /* Initiate Sites variables */
        new Site();

        /* Start the session */
        Session::start();

        /* Initiate System Language */
        self::lang(Options::v('system_lang'));
        new Language();

        /* Initiate Router */
        new Router();

        /* Initiate Vendor */
        new Vendor();

        /* Initiate Modules */
        new Mod();

        /* Load themes configuration */
        Theme::loader();

        /* Run Hooks : init */
        Hooks::run('init');

        /* Attach Hooks : admin_page_notif_action */
        Hooks::attach('admin_page_notif_action', array('System', 'alert'));
    }

    /**
     * System Library Loader.
     * This will include library which is called.
     *
     * @author Puguh Wijayanto <psw@metalgenix.com>
     *
     * @since 0.0.1
     */
    public static function lib($var)
    {
        $file = GX_LIB.$var.'.class.php';
        if (file_exists($file)) {
            include $file;
        }
    }

    public static function lang($vars)
    {
        $file = GX_PATH.'/inc/lang/'.$vars.'.lang.php';
        if (file_exists($file)) {
            include $file;
        }
    }

    public static function config($var)
    {
        $file = GX_PATH.'/inc/config/'.$var.'.php';
        if (file_exists($file)) {
            include $file;
        }
    }

    public static function existConf()
    {
        if (file_exists(GX_PATH.'/inc/config/config.php')) {
            return true;
        } else {
            return false;
        }
    }

    // At the beginning of each page call these functions
    public static function gZip()
    {

        #ob_start(ob_gzhandler);
        // ob_start('Site::minifyHTML');
        ob_start();
        ob_implicit_flush(0);
    }

    // Call this function to output everything as gzipped content.
    public static function Zipped()
    {
        global $HTTP_ACCEPT_ENCODING;

        if (headers_sent()) {
            $encoding = false;
        } elseif (strpos($HTTP_ACCEPT_ENCODING, 'x-gzip') !== false) {
            $encoding = 'x-gzip';
        } elseif (strpos($HTTP_ACCEPT_ENCODING, 'gzip') !== false) {
            $encoding = 'gzip';
        } else {
            $encoding = false;
        }

        if ($encoding) {
            $contents = ob_get_contents();
            ob_end_clean();
            header('Content-Encoding: '.$encoding);
            echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
            $size = strlen($contents);
            $contents = gzcompress($contents, 9);
            $contents = substr($contents, 0, $size);
            echo $contents;
            exit();
        } else {
            ob_end_flush();
            exit();
        }
    }

    public static function admin()
    {
    }

    public static function inc($vars, $data = '')
    {
        $file = GX_PATH.'/gxadmin/inc/'.$vars.'.php';

        if (file_exists($file)) {
            include $file;
        }
    }

    public static function v()
    {
        return self::$version.' '.self::$v_release;
    }

    public static function versionCheck()
    {
        $v = trim(self::latestVersion());

        // print_r($v);
        $v = str_replace('.', '', $v);
        $selfv = str_replace('.', '', self::$version);
        if ($v > $selfv) {
            Hooks::attach('admin_page_notif_action', array('System', 'versionReport'));
        }
    }

    public static function latestVersion()
    {
        $check = json_decode(Options::v('system_check'), true);
        $now = strtotime(date('Y-m-d H:i:s'));

        if (isset($check['last_check'])) {
            $limit = $now - $check['last_check'];

            if ($limit < 86400) {
                $v = $check['version'];
            } else {
                $v = self::getLatestVersion($now);
            }
        } else {
            $v = self::getLatestVersion($now);
        }

        return $v;
    }

    public static function getLatestVersion($now)
    {
        $v = file_get_contents('https://raw.githubusercontent.com/semplon/GeniXCMS/master/VERSION');

        $arr = array(
                'version' => trim($v),
                'last_check' => $now,
            );
        $arr = json_encode($arr);

        Options::update('system_check', $arr);

        return $v;
    }

    public static function versionReport()
    {
        $v = self::latestVersion();

        $html = "
        <div class=\"alert alert-warning\">
            <span class=\"fa fa-warning\"></span> Warning: Your CMS version is different with our latest version (<strong>$v</strong>).
            Please upgrade your system.
        </div>
        ";

        return $html;
    }

    public static function alert()
    {
        global $data;
        // print_r($data);
        if (isset($data['alertSuccess'])) {
            # code...
            echo '<div class="alert alert-success" >
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
                <span class="sr-only">Close</span>
            </button>
            <ul class="list-unstyled">';
            foreach ($data['alertSuccess'] as $alert) {
                # code...
                echo "<li>$alert</li>\n";
            }
            echo '</ul></div>';
        }
        if (isset($data['alertDanger'])) {
            # code...
            echo '<div class="alert alert-danger" >
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
                <span class="sr-only">Close</span>
            </button>
            <ul class="list-unstyled">';
            foreach ($data['alertDanger'] as $alert) {
                # code...
                echo "<li>$alert</li>";
            }
            echo '</ul></div>';
        }
        if (isset($data['alertInfo'])) {
            # code...
            echo '<div class="alert alert-info" >
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
                <span class="sr-only">Close</span>
            </button>
            <ul class="list-unstyled">';
            foreach ($data['alertInfo'] as $alert) {
                # code...
                echo "$alert\n";
            }
            echo '</ul></div>';
        }
        if (isset($data['alertWarning'])) {
            # code...
            echo '<div class="alert alert-warning" >
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
                <span class="sr-only">Close</span>
            </button>
            <ul class="list-unstyled">';
            foreach ($data['alertWarning'] as $alert) {
                # code...
                echo "$alert\n";
            }
            echo '</ul></div>';
        }
        if (isset($data['alertDefault'])) {
            # code...
            echo '<div class="alert alert-default" >
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
                <span class="sr-only">Close</span>
            </button>
            <ul class="list-unstyled">';
            foreach ($data['alertDefault'] as $alert) {
                # code...
                echo "$alert\n";
            }
            echo '</ul></div>';
        }
    }
}

/* End of file system.class.php */
/* Location: ./inc/lib/system.class.php */
