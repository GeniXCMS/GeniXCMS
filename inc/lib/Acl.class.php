<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 2.0.0
 * @version 2.0.0-alpha
 *
 * @link https://github.com/GeniXCMS/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @author GenixCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2024 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class Acl
{
    public static $perms = [];
    private static $_table_checked = false;

    public static function init()
    {
        if (self::$_table_checked)
            return;

        self::$_table_checked = true;

        // Register core permissions
        self::registerCore();
    }

    public static function register($key, $label, $default_groups = [0, 1, 2])
    {
        self::$perms[$key] = [
            'label' => $label,
            'default' => $default_groups
        ];
    }

    public static function check($permission)
    {
        self::init();
        $group = Session::val('group');

        // Level 0 (Administrator) always has full access
        if ($group === '0' || $group === 0) {
            return true;
        }

        if ($group === null)
            return false;

        return self::checkGroup($permission, $group);
    }

    public static function checkGroup($permission, $group)
    {
        self::init();

        // Level 0 (Administrator) always has full access
        if ($group === '0' || $group === 0) {
            return true;
        }

        // Check if DB has explicit setting
        $res = Query::table('permissions')
            ->select('status')
            ->where('group_id', $group)
            ->where('permission', $permission)
            ->first();

        if ($res && isset($res->status)) {
            return (int) $res->status === 1;
        }

        // Fallback to defaults defined in code
        if (isset(self::$perms[$permission])) {
            return in_array($group, self::$perms[$permission]['default']);
        }

        return false;
    }

    private static function registerCore()
    {
        // Posts
        self::register('POSTS_VIEW', 'View Posts list', [0, 1, 2, 3, 4]);
        self::register('POSTS_ADD', 'Add new Post', [0, 1, 2, 3]);
        self::register('POSTS_EDIT', 'Edit existing Post', [0, 1, 2, 3]);
        self::register('POSTS_DELETE', 'Delete Post', [0, 1]);

        // Pages
        self::register('PAGES_VIEW', 'View Pages list', [0, 1, 2]);
        self::register('PAGES_ADD', 'Add new Page', [0, 1]);
        self::register('PAGES_EDIT', 'Edit existing Page', [0, 1]);
        self::register('PAGES_DELETE', 'Delete Page', [0, 1]);

        // Media
        self::register('MEDIA_VIEW', 'Access Media Manager', [0, 1, 2, 3]);
        self::register('MEDIA_UPLOAD', 'Upload Files', [0, 1, 2, 3]);
        self::register('MEDIA_DELETE', 'Delete Files', [0, 1]);

        // Menus
        self::register('MENUS_MANAGE', 'Manage Navigation Menus', [0, 1]);

        // Themes & Modules
        self::register('THEMES_MANAGE', 'Manage Themes', [0]);
        self::register('MODULES_MANAGE', 'Manage Modules', [0]);

        // System Settings
        self::register('SETTINGS_MANAGE', 'Modify System Settings', [0]);

        // Users
        self::register('USERS_VIEW', 'View User List', [0, 1]);
        self::register('USERS_ADD', 'Create new Users', [0, 1]);
        self::register('USERS_EDIT', 'Edit User Profiles', [0, 1]);
        self::register('USERS_DELETE', 'Delete User Accounts', [0]);
    }

    public static function getAllPermissions()
    {
        self::init();
        return self::$perms;
    }

    public static function set($group_id, $permission, $status)
    {
        self::init();
        // Check if exists
        $res = Query::table('permissions')
            ->select('id')
            ->where('group_id', $group_id)
            ->where('permission', $permission)
            ->first();

        if ($res && isset($res->id)) {
            return Query::table('permissions')
                ->where('group_id', $group_id)
                ->where('permission', $permission)
                ->update(['status' => (int) $status]);
        } else {
            return Query::table('permissions')->insert([
                'group_id' => $group_id,
                'permission' => $permission,
                'status' => (int) $status
            ]);
        }
    }
}
