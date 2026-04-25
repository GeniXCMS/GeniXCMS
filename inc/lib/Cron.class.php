<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * GeniXCMS - Programmable Cron Registry.
 *
 * Virtual Cron system similar to WP-Cron.
 * Handles scheduled tasks in GeniXCMS.
 * @since 2.0.0
 * @version 2.4.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class Cron
{
    /** @var array|null Cached jobs */
    private static $_jobs = null;

    /**
     * Load jobs from options.
     */
    private static function load(): array
    {
        if (self::$_jobs === null) {
            // Use Options::get to ensure we get data directly from DB if updated in same request
            $jobs = Options::get('cron_jobs');
            self::$_jobs = $jobs ? json_decode($jobs, true) : [];
        }
        return self::$_jobs;
    }

    /**
     * Save jobs to options.
     */
    private static function save(): void
    {
        Options::update('cron_jobs', json_encode(self::$_jobs));
    }

    /**
     * Get the next scheduled timestamp for a specific hook.
     *
     * @param string     $hook Hook name.
     * @param array|null $args (Optional) Exact arguments.
     * @return int|false
     */
    public static function getNext(string $hook, ?array $args = null): int|false
    {
        $jobs = self::load();
        $sig = ($args !== null) ? md5(serialize($args)) : null;

        foreach ($jobs as $timestamp => $hooks) {
            if (isset($hooks[$hook])) {
                if ($sig !== null) {
                    if (isset($hooks[$hook][$sig])) {
                        return (int) $timestamp;
                    }
                } else {
                    return (int) $timestamp;
                }
            }
        }
        return false;
    }

    /**
     * Schedule a recurring event.
     *
     * @param int    $timestamp  Unix timestamp when it should run first.
     * @param string $recurrence 'hourly', 'twicedaily', 'daily', 'weekly'.
     * @param string $hook       Action hook to trigger.
     * @param array  $args       Arguments to pass to the hook.
     */
    public static function schedule(int $timestamp, string $recurrence, string $hook, array $args = []): void
    {
        self::load();
        $sig = md5(serialize($args));

        $intervals = self::getSchedules();
        if (!isset($intervals[$recurrence])) {
            return;
        }

        self::$_jobs[$timestamp][$hook][$sig] = [
            'schedule' => $recurrence,
            'interval' => $intervals[$recurrence]['interval'],
            'args' => $args
        ];
        ksort(self::$_jobs);
        self::save();
    }

    /**
     * Schedule a one-time event.
     *
     * @param int    $timestamp  Unix timestamp when it should run.
     * @param string $hook       Action hook to trigger.
     * @param array  $args       Arguments to pass to the hook.
     */
    public static function scheduleOnce(int $timestamp, string $hook, array $args = []): void
    {
        self::load();
        $sig = md5(serialize($args));
        self::$_jobs[$timestamp][$hook][$sig] = [
            'schedule' => false,
            'args' => $args
        ];
        ksort(self::$_jobs);
        self::save();
    }

    /**
     * Unschedule an event.
     *
     * @param int    $timestamp  Timestamp of the event.
     * @param string $hook       Hook name.
     * @param array  $args       Arguments (must match exactly).
     */
    public static function unschedule(int $timestamp, string $hook, array $args = []): void
    {
        self::load();
        $sig = md5(serialize($args));
        if (isset(self::$_jobs[$timestamp][$hook][$sig])) {
            unset(self::$_jobs[$timestamp][$hook][$sig]);

            if (empty(self::$_jobs[$timestamp][$hook])) {
                unset(self::$_jobs[$timestamp][$hook]);
            }
            if (empty(self::$_jobs[$timestamp])) {
                unset(self::$_jobs[$timestamp]);
            }
            self::save();
        }
    }

    /**
     * Clear all scheduled events for a specific hook.
     *
     * @param string $hook Hook name.
     */
    public static function clear(string $hook): void
    {
        self::load();
        $changed = false;
        foreach (self::$_jobs as $timestamp => $hooks) {
            if (isset($hooks[$hook])) {
                unset(self::$_jobs[$timestamp][$hook]);
                $changed = true;
            }
            if (empty(self::$_jobs[$timestamp])) {
                unset(self::$_jobs[$timestamp]);
            }
        }
        if ($changed) {
            self::save();
        }
    }

    /**
     * Execute all due tasks.
     * Should be called on every page load (virtual cron).
     */
    public static function run(): void
    {
        $now = time();
        $jobs = self::load();
        if (empty($jobs))
            return;

        $ran = false;
        foreach ($jobs as $timestamp => $hooks) {
            if ($timestamp > $now)
                break;

            foreach ($hooks as $hook => $sigs) {
                foreach ($sigs as $sig => $data) {

                    // Trigger the action
                    Hooks::run($hook, $data['args']);

                    // Reschedule if recurring
                    if ($data['schedule']) {
                        $next_run = $now + $data['interval'];
                        self::schedule($next_run, $data['schedule'], $hook, $data['args']);
                    }

                    // Remove current processed job
                    unset(self::$_jobs[$timestamp][$hook][$sig]);
                }

                if (empty(self::$_jobs[$timestamp][$hook])) {
                    unset(self::$_jobs[$timestamp][$hook]);
                }
            }

            if (empty(self::$_jobs[$timestamp])) {
                unset(self::$_jobs[$timestamp]);
            }
            $ran = true;
        }

        if ($ran) {
            self::save();
        }
    }

    /**
     * Get available cron schedules.
     *
     * @return array
     */
    public static function getSchedules(): array
    {
        $schedules = [
            'hourly' => ['interval' => 3600, 'display' => _('Once Hourly')],
            'twicedaily' => ['interval' => 43200, 'display' => _('Twice Daily')],
            'daily' => ['interval' => 86400, 'display' => _('Once Daily')],
            'weekly' => ['interval' => 604800, 'display' => _('Once Weekly')],
        ];
        return Hooks::filter('cron_schedules', $schedules);
    }
}
