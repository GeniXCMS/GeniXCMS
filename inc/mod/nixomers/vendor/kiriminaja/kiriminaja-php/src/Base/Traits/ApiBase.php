<?php

namespace KiriminAja\Base\Traits;

use KiriminAja\Base\Api\Api;

trait ApiBase {
    protected bool $useInstant = false;
    /**
     * Getter Api client
     *
     * @param bool $useInstant
     * @return Api
     */
    protected static function api(bool $useInstant = false): Api {
        return new Api($useInstant);
    }

}
