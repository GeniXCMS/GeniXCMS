<?php

namespace KiriminAja\Contracts;

interface PreferenceContract {
    /**
     * @param array $services
     * @return mixed
     */
    public function setWhiteListExpedition(array $services);

    /**
     * @param string $url
     * @return mixed
     */
    public function setCallback(string $url);
}
