<?php

namespace KiriminAja\Responses;

use KiriminAja\Base\ModelBase;

class ServiceResponse extends ModelBase
{
    public bool   $status  = false;
    public string $message = "-";

    /**
     * @var mixed $data
     */
    public        $data;

    /**
     * @param bool $status
     * @param string $message
     * @param $data
     */
    public function __construct(bool $status, string $message, $data)
    {
        $this->status  = $status;
        $this->message = $message;
        $this->data    = $data;
    }
}
