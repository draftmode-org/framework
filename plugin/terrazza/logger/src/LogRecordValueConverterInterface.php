<?php

namespace Terrazza\Logger;

interface LogRecordValueConverterInterface {
    /**
     * @param mixed $value
     * @return mixed|null
     */
    public function getValue($value);
}