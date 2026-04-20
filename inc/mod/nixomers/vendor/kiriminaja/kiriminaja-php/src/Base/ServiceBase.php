<?php

namespace KiriminAja\Base;

use KiriminAja\Contracts\ResponseContract;
use KiriminAja\Contracts\ServiceContract;
use KiriminAja\Responses\ServiceResponse;

abstract class ServiceBase implements ServiceContract, ResponseContract {
    public static function success($data, $message): ServiceResponse {
        return new ServiceResponse(true, $message, $data);
    }

    public static function error($data, $message): ServiceResponse {
        return new ServiceResponse(false, $message, $data);
    }
}
