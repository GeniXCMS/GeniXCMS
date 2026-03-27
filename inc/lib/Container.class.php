<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * Dependency Injection Container for GeniXCMS.
 * 
 * Provides a central registry for core services, enabling decoupling
 * and improved testability by moving away from hard-coded globals and static states.
 *
 * @since 1.1.0
 */
class Container
{
    private static $_services = [];

    /**
     * Map a service to the container.
     */
    public static function set($key, $instance)
    {
        self::$_services[$key] = $instance;
    }

    /**
     * Get a service from the container.
     */
    public static function get($key)
    {
        return self::$_services[$key] ?? null;
    }

    /**
     * Check if a service exists.
     */
    public static function has($key)
    {
        return isset(self::$_services[$key]);
    }

    /**
     * Convenient shortcuts for core services.
     */
    public static function db() { return self::get('db'); }
    public static function user() { return self::get('user'); }
    public static function system() { return self::get('system'); }
    public static function hooks() { return self::get('hooks'); }
}
