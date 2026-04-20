<?php

namespace KiriminAja\Base\Config\Cache;

class _CacheMode
{
    private static string $key = '---KiriminAja-Cached-Mode-Key---';

    /**
     * allowed mode
     *
     * @return array
     */
    private static function allowedMode(): array
    {
        return [Mode::Production, Mode::Staging];
    }

    /**
     * Setter mode
     *
     * @param Mode::string $mode
     * @return void
     * @throws \Exception
     */
    public function setMode($mode): void
    {
        if (!in_array($mode, self::allowedMode())) throw new \Exception("Mode not allowed, allowed mode " . json_encode(self::allowedMode()) . ", your mode $mode");
        Cache::setCache(self::$key, $mode);
    }

    /**
     * Getter key
     *
     * @return mixed|null
     */
    public function getMode(): mixed
    {
        return Cache::getCache(self::$key) ?? Mode::Staging;
    }
}

