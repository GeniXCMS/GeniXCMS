<?php

namespace KiriminAja\Contracts;

use KiriminAja\Responses\ServiceResponse;

interface ResponseContract {
    /**
     * @param $data
     * @param $message
     * @return \KiriminAja\Responses\ServiceResponse
     */
    public static function success($data, $message): ServiceResponse;

    /**
     * @param $data
     * @param $message
     * @return \KiriminAja\Responses\ServiceResponse
     */
    public static function error($data, $message): ServiceResponse;
}
