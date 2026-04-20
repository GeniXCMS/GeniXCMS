<?php

namespace KiriminAja\Base;

abstract class ModelBase {
    /**
     * Getter variables of the class references
     *
     * @return array
     */
    public function toArray(): array {
        return get_object_vars($this);
    }
}