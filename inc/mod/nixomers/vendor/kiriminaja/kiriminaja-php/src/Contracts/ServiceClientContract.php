<?php

namespace KiriminAja\Contracts;

use GuzzleHttp\Client;

interface ServiceClientContract {
    /**
     * @param string $endPoint
     * @param $data
     * @return mixed
     */
    public function get(string $endPoint, $data);

    /**
     * @param string $endPoint
     * @param $data
     * @return mixed
     */
    public function post(string $endPoint, $data);

    /**
     * @param string $endPoint
     * @param $data
     * @return mixed
     */
    public function put(string $endPoint, $data);

    /**
     * @param string $endPoint
     * @param $data
     * @return mixed
     */
    public function delete(string $endPoint, $data);
}
