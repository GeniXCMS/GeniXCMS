<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 0.0.1 build date 20140928
 * @version 2.2.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class Mod
{
    public static $listMenu = array();

    /**
     * Mod Constructor.
     * Triggers the module loader to initialize active modules.
     */
    public function __construct()
    {
        self::loader();
    }

    /**
     * Alias for self::load().
     *
     * @param string $var Module directory name.
     */
    public static function mod($var)
    {
        self::load($var);
    }

    /**
     * Includes the options file for a specific module.
     *
     * @param string $var  Module directory name.
     * @param array  $data Optional data to pass to the options file (not explicitly used in base).
     */
    public static function options($var, $data = array())
    {
        $file = GX_MOD . $var . '/options.php';
        if (file_exists($file)) {
            include($file);
        }
    }

    /**
     * Scans the GX_MOD directory and returns an array of valid modules.
     * A module is considered valid if it contains an index.php file.
     *
     * @return array List of valid module directory names.
     */
    public static function modList()
    {
        $mod = array();
        $handle = dir(GX_MOD);
        while (false !== ($entry = $handle->read())) {
            if ($entry != '.' && $entry != '..') {
                $dir = GX_MOD . $entry;
                if (is_dir($dir) == true) {
                    (file_exists($dir . '/index.php')) ? $mod[] = basename($dir) : '';
                }
            }
        }

        $handle->close();

        return $mod;
    }

    /**
     * Parses the index.php file of a module to extract metadata.
     * Uses regex to find tags like Name, Desc, Version, etc.
     *
     * @param string $vars Module directory name.
     * @return array|string Array of metadata or empty string if file not found.
     */
    public static function data($vars)
    {
        $file = GX_MOD . '/' . $vars . '/index.php';
        if (file_exists($file)) {
            $handle = fopen($file, 'r');
            $data = fread($handle, filesize($file));
            fclose($handle);
            preg_match('/\* Name: (.*)\s\*/Us', $data, $matches);
            $d['name'] = $matches[1] ?? 'Untitled Module';
            preg_match('/\* Desc: (.*)\s\*/Us', $data, $matches);
            $d['desc'] = $matches[1] ?? '';
            preg_match('/\* Version: (.*)\s\*/Us', $data, $matches);
            $d['version'] = $matches[1] ?? '0.0.0';
            preg_match('/\* Build: (.*)\s\*/Us', $data, $matches);
            $d['build'] = $matches[1] ?? '0';
            preg_match('/\* Developer: (.*)\s\*/Us', $data, $matches);
            $d['developer'] = $matches[1] ?? 'Unknown';
            preg_match('/\* URI: (.*)\s\*/Us', $data, $matches);
            $d['url'] = $matches[1] ?? '';
            preg_match('/\* License: (.*)\s\*/Us', $data, $matches);
            $d['license'] = $matches[1] ?? 'None';
            preg_match('/\* Icon: (.*)\s\*/Us', $data, $matches);
            $d['icon'] = $matches[1] ?? 'bi bi-puzzle';
        } else {
            $d = '';
        }

        return $d;
    }

    /**
     * Renders the HTML menu list for active modules in the admin sidebar.
     *
     * @return string Generated HTML <li> list.
     */
    public static function modMenu()
    {
        $json = Options::v('modules');
        $mod = json_decode($json ?? '', true);
        if (is_array($mod)) {
            $list = '';
            asort($mod);
            foreach ($mod as $m) {
                if (self::exist($m)) {
                    $data = self::data($m);
                    $class = (isset($_GET['mod']) && $_GET['mod'] == $m) ? 'active' : '';
                    $icon = isset($data['icon']) && $data['icon'] ? $data['icon'] : 'bi bi-puzzle';
                    $list .= "
                    <li class=\"{$class}\">
                        <a href=\"index.php?page=mods&mod={$m}\">
                            <i class=\"{$icon}\"></i> <span>" . $data['name'] . "</span>
                        </a>
                    </li>
                    ";
                }
            }
        } else {
            $list = '';
        }

        return $list;
    }

    /**
     * Includes a module partial file and returns its buffered output.
     * Automatically extracts the provided data array into local variables.
     *
     * @param string $vars Content filename (without .php).
     * @param array  $data Data to extract into the file.
     * @param string $dir  Directory containing the file.
     * @return string      Captured HTML/content output.
     */
    public static function inc($vars, $data, $dir)
    {
        $file = $dir . '/' . $vars . '.php';
        $content = '';
        if (file_exists($file)) {
            ob_start();
            $d = $data;
            if (is_array($data)) {
                extract($data);
            }
            $data = $d;
            include $file;
            $content = ob_get_clean();
        }

        return $content;
    }

    /**
     * Activates a module by adding it to the 'modules' option array.
     *
     * @param string $mod Module directory name.
     * @return bool       True on success, false on failure.
     */
    public static function activate($mod)
    {
        $json = Options::v('modules');
        $mods = json_decode($json ?? '', true);
        //print_r($mods);
        if (!is_array($mods) || $mods == '') {
            $mods = array();
        }
        if (!in_array($mod, $mods)) {
            $mods = array_merge($mods, array($mod));
        }

        $mods = json_encode($mods);

        $mods = Options::update('modules', $mods);
        if ($mods) {
            new Options();

            return true;
        } else {
            return false;
        }
    }

    /**
     * Deactivates a module by removing it from the 'modules' option array.
     *
     * @param string $mod Module directory name.
     * @return bool       True on success, false on failure.
     */
    public static function deactivate($mod)
    {
        $mods = Options::v('modules');
        $mods = json_decode($mods ?? '', true);
        if (!is_array($mods) || $mods == '') {
            $mods = array();
        }
        //print_r($mods);
        $arr = [];
        for ($i = 0; $i < count($mods); ++$i) {
            if ($mods[$i] == $mod) {
                //unset($mods[$i]);
            } else {
                $arr[] = $mods[$i];
            }
        }
        //print_r($arr);
        //asort($mods);
        $mods = json_encode($arr);
        $mods = Options::update('modules', $mods);
        if ($mods) {
            new Options();

            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks if a specific module is currently activated.
     *
     * @param string $mod Module directory name.
     * @return bool       True if active, false otherwise.
     */
    public static function isActive($mod)
    {
        $json = Options::v('modules');
        $mods = json_decode($json ?? '', true);
        //print_r($mods);
        if (!is_array($mods) || $mods == '') {
            $mods = array();
        }

        if (in_array($mod, $mods)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Orchestrates the module lifecycle.
     * - Handles activation/deactivation/removal requests via $_GET in the admin panel.
     * - Iterates over all active modules and executes their index.php files.
     *
     * @return array Empty array (legacy return).
     */
    public static function loader()
    {
        $data = [];
        if (User::access(0)) {
            if (isset($_GET['page']) && $_GET['page'] == 'modules') {
                $token = isset($_GET['token']) ? Typo::cleanX($_GET['token']) : '';
                $modules = isset($_GET['modules']) ? Typo::cleanX($_GET['modules']) : '';
                if (isset($_GET['act'])) {
                    if ($_GET['act'] == 'activate') {
                        if (!Token::validate($token, true)) {
                            // VALIDATE ALL
                            $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
                        }

                        if (!isset($alertDanger)) {
                            self::activate($modules);
                            $GLOBALS['alertSuccess'] = _('Module Activated');
                        } else {
                            $GLOBALS['alertDanger'] = $alertDanger;
                        }
                    } elseif ($_GET['act'] == 'deactivate') {
                        if (!Token::validate($token, true)) {
                            // VALIDATE ALL
                            $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
                        }

                        if (!isset($alertDanger)) {
                            self::deactivate($modules);
                            $GLOBALS['alertSuccess'] = _('Module Deactivated');
                        } else {
                            $GLOBALS['alertDanger'] = $alertDanger;
                        }
                    } elseif ($_GET['act'] == 'remove') {
                        if (!Token::validate($token, true)) {
                            // VALIDATE ALL
                            $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
                        }
                        if (self::isActive($modules)) {
                            $alertDanger[] = _('Module is Active. Please deactivate first.');
                        }
                        if (!isset($alertDanger)) {
                            self::deactivate($modules);
                            if (false != Files::delTree(GX_MOD . $_GET['modules'])) {
                                $GLOBALS['alertSuccess'] = _('Module Deleted');
                            } else {
                                $GLOBALS['alertDanger'][] = _("Can't delete module files");
                            }
                        } else {
                            $GLOBALS['alertDanger'] = $alertDanger;
                        }
                    }
                }
            }
        }
        $json = Options::v('modules');
        $mods = json_decode($json ?? '', true);
        if (!is_array($mods) || $mods == '') {
            $mods = array();
        }
        foreach ($mods as $m) {
            if (self::exist($m)) {
                self::load($m);
            }
        }

        return $data;
    }

    /**
     * Includes the core entry point (index.php) for a specific module.
     *
     * @param string $mod Module directory name.
     */
    public static function load($mod)
    {
        $file = GX_MOD . '/' . $mod . '/index.php';
        if (file_exists($file)) {
            include $file;
        }
    }

    /**
     * Generates the public URL for a specific module's assets/directory.
     *
     * @param string $mod Module directory name.
     * @return string      The public URL.
     */
    public static function url($mod)
    {
        $url = Site::$url . '/inc/mod/' . $mod;

        return $url;
    }

    /**
     * Checks if a module's options file exists.
     *
     * @param string $mod Module directory name.
     * @return bool       True if exists, false otherwise.
     */
    public static function exist($mod)
    {
        $file = GX_MOD . '/' . $mod . '/options.php';
        if (file_exists($file)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Retrieves the display name of a module from its metadata.
     *
     * @param string $mod Module directory name.
     * @return string      The module name.
     */
    public static function name($mod)
    {
        $data = self::data($mod);
        $name = isset($data['name']) ? $data['name'] : '';
        return $name;
    }

    /**
     * Outputs HTML <option> tags for the registered module menus.
     * Used for selectors in the admin panel.
     *
     * @param string $var Pre-selected menu key (optional).
     */
    public static function menuList($var = '')
    {
        $list = self::$listMenu;

        foreach ($list as $k => $v) {
            if ($var != '' && $k == $var) {
                $selected = 'selected';
            } else {
                $selected = '';
            }
            echo "<option value=\"{$k}\" {$selected}>{$v}</option>";
        }
    }

    /**
     * Registers new menu items into the module menu list.
     *
     * @param array $menus Array of menu keys and titles.
     * @return bool        Always true.
     */
    public static function addMenuList($menus)
    {
        self::$listMenu = array_merge(self::$listMenu, $menus);

        return true;
    }

    /**
     * Retrieves the descriptive title for a specific registered module menu.
     *
     * @param string $mod Menu key.
     * @return string      Title of the menu.
     */
    public static function getTitle($mod)
    {
        $title = self::$listMenu;
        $titlemenu = array_key_exists($mod, $title) ? $title[$mod] : "";
        return $titlemenu;
    }
}

/* End of file Mod.class.php */
/* Location: ./inc/lib/Mod.class.php */
