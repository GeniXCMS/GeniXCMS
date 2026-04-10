<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 0.0.1 build date 20140925
 * @version 2.2.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class Theme
{
    public static $url;
    public static $active;

    /**
     * Theme Constructor.
     * Initializes theme URL, active theme identifier, and triggers the theme loader.
     */
    public function __construct()
    {
        global $GLOBALS;
        self::$url = Url::theme();
        self::$active = Options::v('themes');
        self::loader();
    }

    /**
     * Includes a theme file and optionally exposes a data buffer.
     * Triggers a critical layout error if the file is missing.
     *
     * @param string $var  Theme file name (without extension).
     * @param mixed  $data Data to be exposed via $GLOBALS['data'].
     */
    public static function theme($var, $data = '')
    {
        if (isset($data)) {
            $GLOBALS['data'] = $data;
        }
        if (self::exist($var)) {
            include GX_THEME . THEME . '/' . $var . '.php';
        } else {
            Control::error('unknown', _('Theme file is missing.'));
        }
    }

    /**
     * Alias for theme().
     *
     * @param string $var  File name.
     * @param mixed  $data Data.
     */
    public static function inc($var, $data = '')
    {
        self::theme($var, $data);
    }

    /**
     * Checks if a theme file (PHP or Latte) exists in the active theme directory.
     *
     * @param string $vars File name (without extension).
     * @return bool        True if exists.
     */
    public static function exist($vars)
    {
        if (file_exists(GX_THEME . THEME . '/' . $vars . '.php') || file_exists(GX_THEME . THEME . '/' . $vars . '.latte')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Includes an admin theme file.
     *
     * @param string $var  File name.
     * @param mixed  $data Data.
     */
    public static function admin($var, $data = '')
    {
        if (isset($data)) {
            $GLOBALS['data'] = $data;
        }
        $admin_dir = defined('ADMIN_DIR') ? ADMIN_DIR : 'gxadmin';
        include GX_PATH . '/' . $admin_dir . '/themes/' . $var . '.php';
    }

    /**
     * Renders the theme header with standard HTTP cache and encoding headers.
     *
     * @param mixed $vars Data to pass to header.php.
     */
    public static function header($vars = '')
    {
        header('Cache-Control: must-revalidate,max-age=300,s-maxage=900');
        $offset = 60 * 60 * 24 * 3;
        $ExpStr = 'Expires: ' . gmdate('D, d M Y H:i:s', time() + $offset) . ' GMT';
        header($ExpStr);
        header('Content-Type: text/html; charset=utf-8');

        if (isset($vars)) {
            $GLOBALS['data'] = $vars;
            self::theme('header', $vars);
        } else {
            self::theme('header');
        }
    }
    /**
     * Renders the theme footer.
     *
     * @param mixed $vars Data to pass to footer.php.
     */
    public static function footer($vars = '')
    {
        global $GLOBALS;
        if (isset($vars)) {
            $GLOBALS['data'] = $vars;
            self::theme('footer', $vars);
        } else {
            self::theme('footer');
        }
    }

    /**
     * Initializes the content editor (Summernote or GxEditor).
     * Configures the global editor state and triggers Asset/Editor initialisation.
     *
     * @param string $mode   Toolbar complexity ('mini', 'light', 'full').
     * @param int    $height Editor height in pixels (default: 300).
     * @param array  $blocks Configuration blocks for GxEditor.
     */
    public static function editor($mode = 'light', $height = '300', $blocks = [])
    {
        $editor = Options::v('use_editor');
        $editor_type = Options::v('editor_type') ?: 'summernote';

        if (empty($blocks) && $editor_type === 'gxeditor') {
            $blocks = json_decode(Options::v('gxeditor_full_blocks') ?? Options::v('gxeditor_post_blocks') ?? '', true);
        }
        if ($height === '300' && Options::v('gxeditor_height') && $editor_type === 'gxeditor') {
            $height = Options::v('gxeditor_height');
        }

        if ($editor == 'on') {
            $GLOBALS['editor'] = true;
            $GLOBALS['editor_type'] = $editor_type;
            $GLOBALS['editor_blocks'] = $blocks;
            System::toolbarMode($mode);
            System::toolbar($mode);
            $GLOBALS['editor_height'] = $height;
            Editor::init();
        } else {
            $GLOBALS['editor'] = false;
        }
        //return $editor;
    }

    public static function validator($vars = '')
    {
        $GLOBALS['validator'] = true;
        $GLOBALS['validator_js'] = $vars;
        //return $editor;
    }

    public static function install($var)
    {
        $admin_dir = defined('ADMIN_DIR') ? ADMIN_DIR : 'gxadmin';
        include GX_PATH . '/' . $admin_dir . '/themes/install/' . $var . '.php';
    }

    public static function auth($var, $data = '')
    {
        $admin_dir = defined('ADMIN_DIR') ? ADMIN_DIR : 'gxadmin';
        include GX_PATH . '/' . $admin_dir . '/themes/auth/' . $var . '.php';
    }

    /**
     * Includes a theme's options.php file for administrative configuration.
     *
     * @param string $var Theme directory name.
     */
    public static function options($var)
    {
        if (self::optionsExist($var)) {
            include GX_THEME . $var . '/options.php';
        }
    }

    /**
     * Checks if a theme has an options.php file.
     *
     * @param string $var Theme directory name.
     * @return bool       True if exists.
     */
    public static function optionsExist($var)
    {
        if (file_exists(GX_THEME . $var . '/options.php')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Includes a theme's function.php file (the theme bootstrapper).
     *
     * @param string $var Theme directory name.
     */
    public static function incFunc($var)
    {
        if (self::functionExist($var)) {
            include GX_THEME . $var . '/function.php';
        }
    }

    /**
     * Checks if a theme has a function.php file.
     *
     * @param string $var Theme directory name.
     * @return bool       True if exists.
     */
    public static function functionExist($var)
    {
        if (file_exists(GX_THEME . $var . '/function.php')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Scans and returns a list of installed themes containing a themeinfo.php file.
     *
     * @return array List of theme directory names.
     */
    public static function thmList()
    {
        //$mod = '';
        $handle = dir(GX_THEME);
        $thm = [];
        while (false !== ($entry = $handle->read())) {
            if ($entry != '.' && $entry != '..') {
                $dir = GX_THEME . $entry;
                if (is_dir($dir) == true && file_exists($dir . '/themeinfo.php')) {
                    $thm[] = basename($dir);
                }
            }
        }

        $handle->close();

        return $thm;
    }

    /**
     * Activates a theme by updating the system options.
     *
     * @param string $thm Theme directory name.
     * @return bool       True on success.
     */
    public static function activate($thm)
    {
        if (Options::update('themes', Typo::cleanX($thm))) {
            new Options();

            return true;
        } else {
            return false;
        }
    }

    /**
     * Parses the themeinfo.php file and returns metadata (Name, URI, Developer, etc).
     *
     * @param string $vars Theme directory name.
     * @return array       Associative array of theme metadata.
     */
    public static function data($vars)
    {
        $file = GX_THEME . '/' . $vars . '/themeinfo.php';
        $handle = fopen($file, 'r');
        $data = fread($handle, filesize($file));
        fclose($handle);
        $d['name'] = preg_match('/\* Name: (.*)\s\*/Us', $data, $matches) ? $matches[1] : 'Unknown';
        $d['desc'] = preg_match('/\* Desc: (.*)\s\*/Us', $data, $matches) ? $matches[1] : '';
        $d['version'] = preg_match('/\* Version: (.*)\s\*/Us', $data, $matches) ? $matches[1] : '0';
        $d['build'] = preg_match('/\* Build: (.*)\s\*/Us', $data, $matches) ? $matches[1] : '0';
        $d['developer'] = preg_match('/\* Developer: (.*)\s\*/Us', $data, $matches) ? $matches[1] : '';
        $d['url'] = preg_match('/\* URI: (.*)\s\*/Us', $data, $matches) ? $matches[1] : '';
        $d['license'] = preg_match('/\* License: (.*)\s\*/Us', $data, $matches) ? $matches[1] : '';
        $d['icon'] = preg_match('/\* Icon: (.*)\s\*/Us', $data, $matches) ? $matches[1] : '';

        return $d;
    }

    public static function isActive($thm)
    {
        if (Options::v('themes') === $thm) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Master loader for the active theme.
     * Defines the THEME constant and includes the theme's function.php.
     */
    public static function loader()
    {
        $theme = Options::v('themes');
        define('THEME', $theme);
        self::incFunc($theme);
    }

    /**
     * Renders a sidebar menu item for the active theme's options panel.
     *
     * @return string HTML list item.
     */
    public static function thmMenu()
    {
        $thm = Options::v('themes');
        $list = '';

        if (User::access(0)) {
            $data = self::data($thm);
            if (self::optionsExist($thm)) {
                $active = (isset($_GET['page'])
                    && $_GET['page'] == 'themes'
                    && isset($_GET['view'])
                    && $_GET['view'] == 'options') ? 'active' : '';
                $icon = isset($data['icon']) && $data['icon'] ? $data['icon'] : 'bi bi-palette2';
                $list .= "
                <li class=\"{$active}\">
                    <a href=\"index.php?page=themes&view=options\">
                        <i class=\"{$icon}\"></i> <span>" . $data['name'] . "</span>
                    </a>
                </li>";
            }
        }

        return $list;
    }

    /**
     * Retrieves the human-readable name of a theme/module.
     *
     * @param string $mod Theme/Module identifier.
     * @return string     Human name.
     */
    public static function name($mod)
    {
        $data = self::data($mod);

        return $data['name'];
    }

    /**
     * Discovers theme-specific layout templates.
     * Files must follow the naming convention: layout-{slug}.latte or layout-{slug}.php.
     * Optionally extracts a human name from the "Layout: Name" comment.
     *
     * @param string|null $theme Theme directory name (null for active).
     * @return array              Associative array of slug => Human Name.
     */
    public static function getLayouts($theme = null)
    {
        $theme = $theme ?? self::$active;
        $dir = GX_THEME . $theme . '/';
        $layouts = [];

        if (!is_dir($dir))
            return $layouts;

        $files = scandir($dir);
        foreach ($files as $file) {
            if (preg_match('/^layout-(.*)\.(latte|php)$/', $file, $matches)) {
                $slug = $matches[1];
                $name = ucwords(str_replace('-', ' ', $slug));

                // Optional: Extract name from file content {* Layout: Name *}
                $path = $dir . $file;
                if (is_file($path)) {
                    $content = file_get_contents($path, false, null, 0, 1024);
                    if (preg_match('/Layout:\s*([^\s\*\}]+.*)/', $content, $nameMatch)) {
                        $name = trim($nameMatch[1]);
                        $name = preg_replace('/\s*\*?\}/', '', $name);
                    }
                }
                $layouts[$slug] = $name;
            }
        }
        return $layouts;
    }
}

/* End of file Theme.class.php */
/* Location: ./inc/lib/Theme.class.php */
