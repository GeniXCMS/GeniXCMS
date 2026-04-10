<?php
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 2.0.0
 * @version 2.2.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

if (User::access(0)) {
    $data['sitetitle'] = _('System Health');

    // System Requirements Check
    $req = [
        'PHP Version' => [
            'req' => '>= 8.2',
            'val' => PHP_VERSION,
            'status' => version_compare(PHP_VERSION, '8.2.0', '>=')
        ],
        'PDO Extension' => [
            'req' => 'Enabled',
            'val' => extension_loaded('pdo') ? 'Enabled' : 'Disabled',
            'status' => extension_loaded('pdo')
        ],
        'GD Extension' => [
            'req' => 'Enabled',
            'val' => extension_loaded('gd') ? 'Enabled' : 'Disabled',
            'status' => extension_loaded('gd')
        ],
        'ZipArchive' => [
            'req' => 'Enabled',
            'val' => extension_loaded('zip') ? 'Enabled' : 'Disabled',
            'status' => extension_loaded('zip')
        ],
        'cURL' => [
            'req' => 'Enabled',
            'val' => extension_loaded('curl') ? 'Enabled' : 'Disabled',
            'status' => extension_loaded('curl')
        ],
        'OpenSSL' => [
            'req' => 'Enabled',
            'val' => extension_loaded('openssl') ? 'Enabled' : 'Disabled',
            'status' => extension_loaded('openssl')
        ],
        'File Uploads' => [
            'req' => 'Enabled',
            'val' => ini_get('file_uploads') ? 'Enabled' : 'Disabled',
            'status' => ini_get('file_uploads')
        ],
        'Post Max Size' => [
            'req' => '>= 8M',
            'val' => ini_get('post_max_size'),
            'status' => true // informative
        ],
        'Max Execution Time' => [
            'req' => '>= 30s',
            'val' => ini_get('max_execution_time') . 's',
            'status' => true // informative
        ]
    ];

    // Allow modules to add their own requirements
    $req = Hooks::filter('system_health_requirements', $req);

    $data['requirements'] = $req;

    Theme::admin('header', $data);
    System::inc('health', $data);
    Theme::admin('footer');
} else {
    Control::error('noaccess');
}
