<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 0.0.2 build date 20150313
 * @version 2.1.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class Files
{
    /**
     * Recursively deletes a directory and all of its contents (files and subdirectories).
     *
     * @param string $dir The absolute path to the directory.
     * @return bool       True if deleted successfully.
     */
    public static function delTree($dir)
    {
        try {
            $files = array_diff(scandir($dir), array('.', '..'));
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? self::delTree("$dir/$file") : @unlink("$dir/$file");
            }
            rmdir($dir);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @deprecated 2.0.0 Assets are now managed via the Asset class.
     * @return string
     */
    public static function elfinderLib()
    {
        return '';
    }

    /**
     * Scans a file (local or remote) for potentially malicious code patterns.
     * Checks for common PHP injection, base64 encoding, and dangerous HTML/JS tags.
     *
     * @param string $file Path to the file or URL.
     * @return bool        True if the file is considered clean.
     */
    public static function isClean($file): bool
    {
        if (!is_string($file) || empty($file)) {
            return false;
        }

        if (self::isRemote($file)) {
            if (!self::remoteExist($file)) {
                return false;
            }
        } elseif (!file_exists($file)) {
            return false;
        }

        $handle = @fopen($file, 'r');
        if (!$handle) {
            return false;
        }

        if (self::isRemote($file)) {
            # code...
            if (self::remoteExist($file)) {
                $contents = fread($handle, 9064);
            }
        } else {
            if (file_exists($file)) {
                $contents = fread($handle, 9064);
            }
        }

        fclose($handle);

        if (preg_match('/(base64_|eval|system|shell_|exec|php_)/i', $contents)) {
            return false;
        } elseif (preg_match("#&\#x([0-9a-f]+);#i", $contents)) {
            return false;
        } elseif (preg_match('#&\#([0-9]+);#i', $contents)) {
            return false;
        } elseif (preg_match("#([a-z]*)=([\`\'\"]*)script:#iU", $contents)) {
            return false;
        } elseif (preg_match("#([a-z]*)=([\`\'\"]*)javascript:#iU", $contents)) {
            return false;
        } elseif (preg_match("#([a-z]*)=([\'\"]*)vbscript:#iU", $contents)) {
            return false;
        } elseif (preg_match("#(<[^>]+)style=([\`\'\"]*).*expression\([^>]*>#iU", $contents)) {
            return false;
        } elseif (preg_match("#(<[^>]+)style=([\`\'\"]*).*behaviour\([^>]*>#iU", $contents)) {
            return false;
        } elseif (preg_match('#</*(applet|link|style|script|iframe|frame|frameset|html|body|title|div|p|form)*>#i', $contents)) {
            return false;
        } elseif (preg_match('#<\?(.*)\?>#i', $contents)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Checks if a remote URL exists by performing a HEAD request via cURL.
     *
     * @param string $url The URL to check.
     * @return bool       True if the server returns a 200 OK status.
     */
    public static function remoteExist($url)
    {
        $curl = curl_init($url);

        //don't fetch the actual page, you only want to check the connection is ok
        curl_setopt($curl, CURLOPT_NOBODY, true);

        //do request
        $result = curl_exec($curl);

        $ret = false;

        //if request did not fail
        if ($result !== false) {
            //if request was ok, check response code
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($statusCode == 200) {
                $ret = true;
            }
        }

        curl_close($curl);

        return $ret;
    }

    /**
     * Determines if a given string is a remote URL or a local file path.
     *
     * @param string $path The path to check.
     * @return bool        True if it is a remote path (contains //).
     */
    public static function isRemote($path)
    {
        if (strpos($path, '//') !== false) {
            if (strpos($path, '//') >= max(strpos($path, '.'), strpos($path, '/'))) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }
}
