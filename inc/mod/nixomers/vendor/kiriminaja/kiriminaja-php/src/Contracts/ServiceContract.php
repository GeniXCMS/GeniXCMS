<?php

namespace KiriminAja\Contracts;

use KiriminAja\Responses\ServiceResponse;

interface ServiceContract {
    /**
     * @return \KiriminAja\Responses\ServiceResponse
     */
    public function call(): ServiceResponse;
}
