<?php
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * @since 2.0.0
 * @version 2.2.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

class Security
{
    /**
     * Scan a ZIP file for malicious code patterns.
     *
     * @param string $zipPath The absolute path to the ZIP file.
     * @return array Returns an array with 'status' (bool) and 'errors' (array).
     */
    public static function scanZip($zipPath)
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            return ['status' => false, 'errors' => [_('Cannot open ZIP file for scanning.')]];
        }

        $errors = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);

            // Skip directories
            if (substr($filename, -1) == '/')
                continue;

            $content = $zip->getFromIndex($i);
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($extension, ['php', 'phtml', 'php5', 'php7', 'php8'])) {
                $fileErrors = self::scanPhp($content, $filename);
                if (!empty($fileErrors)) {
                    $errors = array_merge($errors, $fileErrors);
                }
            } elseif (in_array($extension, ['js'])) {
                $fileErrors = self::scanJs($content, $filename);
                if (!empty($fileErrors)) {
                    $errors = array_merge($errors, $fileErrors);
                }
            }
        }

        $zip->close();
        return [
            'status' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Scan PHP content for dangerous patterns and critical execution functions.
     *
     * @param string $content  The file content to scan.
     * @param string $filename The name of the file for reporting.
     * @return array           List of found security violations.
     */
    private static function scanPhp($content, $filename)
    {
        $errors = [];

        // Block critical execution functions
        $blocked = [
            'eval\(',
            'passthru\(',
            'shell_exec\(',
            'system\(',
            'exec\(',
            'popen\(',
            'proc_open\(',
            'pcntl_exec\(',
            'create_function\(',
            'assert\(',
            'base64_decode\(',
            'gzinflate\('
        ];

        foreach ($blocked as $pattern) {
            if (preg_match('/' . $pattern . '/Ui', $content)) {
                $errors[] = sprintf(_("Security Alert: Dangerous function '%s' found in '%s'."), str_replace('\\', '', $pattern), $filename);
            }
        }

        // Check for shell execution via backticks
        if (preg_match('/`.*`/U', $content)) {
            $errors[] = sprintf(_("Security Alert: Shell execution via backticks found in '%s'."), $filename);
        }

        return $errors;
    }

    /**
     * Scan Javascript content for potentially dangerous or suspicious patterns.
     *
     * @param string $content  The file content to scan.
     * @param string $filename The name of the file for reporting.
     * @return array           List of found security violations.
     */
    private static function scanJs($content, $filename)
    {
        $errors = [];

        // Suspicious JS patterns
        $blocked = [
            'eval\(',
            'atob\(',
            'String\.fromCharCode\(',
            'unescape\('
        ];

        foreach ($blocked as $pattern) {
            if (preg_match('/' . $pattern . '/Ui', $content)) {
                $errors[] = sprintf(_("Security Alert: Suspicious Javascript pattern '%s' found in '%s'."), str_replace('\\', '', $pattern), $filename);
            }
        }

        // Check for heavily obfuscated JS (common in malware)
        if (strlen($content) > 1000 && !preg_match_all('/[a-zA-Z]/', $content, $matches, PREG_SET_ORDER) / strlen($content) < 0.4) {
            // $errors[] = sprintf(_("Security Alert: Potential obfuscated Javascript in '%s'."), $filename);
        }

        return $errors;
    }
}
