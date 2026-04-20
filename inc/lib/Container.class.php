<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * Dependency Injection Container for GeniXCMS.
 *
 * Provides a central registry for core services, enabling decoupling
 * and improved testability by moving away from hard-coded globals and static states.
 * @since 2.0.0
 * @version 2.3.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class Container
{
    private static $_services = [];

    /**
     * Maps a service or object instance to the container.
     *
     * @param string $key      Unique identifier for the service.
     * @param mixed  $instance The object instance to store.
     */
    public static function set($key, $instance)
    {
        self::$_services[$key] = $instance;
    }

    /**
     * Retrieves a service or object instance from the container.
     *
     * @param string $key Unique identifier for the service.
     * @return mixed|null The registered service instance or null if not found.
     */
    public static function get($key)
    {
        return self::$_services[$key] ?? null;
    }

    /**
     * Checks if a service is currently registered in the container.
     *
     * @param string $key Unique identifier for the service.
     * @return bool       True if the service exists.
     */
    public static function has($key)
    {
        return isset(self::$_services[$key]);
    }

    /**
     * Convenient shortcuts for core services.
     */
    /**
     * Shortcut to retrieve the 'db' (database) service.
     *
     * @return object|null
     */
    public static function db()
    {
        return self::get('db');
    }
    /**
     * Shortcut to retrieve the 'user' management service.
     *
     * @return object|null
     */
    public static function user()
    {
        return self::get('user');
    }
    /**
     * Shortcut to retrieve the 'system' core service.
     *
     * @return object|null
     */
    public static function system()
    {
        return self::get('system');
    }
    /**
     * Shortcut to retrieve the 'hooks' management service.
     *
     * @return object|null
     */
    public static function hooks()
    {
        return self::get('hooks');
    }
}
