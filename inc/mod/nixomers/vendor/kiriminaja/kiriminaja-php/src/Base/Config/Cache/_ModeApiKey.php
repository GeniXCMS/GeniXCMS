<?php

namespace KiriminAja\Base\Config\Cache;

class _ModeApiKey {

    /**
     * Getter cache mode
     *
     * @return _CacheMode
     */
    public function mode(): _CacheMode {
        return new _CacheMode;
    }

    /**
     * Getter api key
     *
     * @return _CacheApiKey
     */
    public function apiKey(): _CacheApiKey {
        return new _CacheApiKey;
    }

}