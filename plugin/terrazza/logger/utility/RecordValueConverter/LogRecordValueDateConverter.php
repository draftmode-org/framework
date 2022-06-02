<?php

namespace Terrazza\Logger\Utility\RecordValueConverter;
use DateTime;
use Terrazza\Logger\LogRecordValueConverterInterface;

class LogRecordValueDateConverter implements LogRecordValueConverterInterface {
    private string $dateFormat;

    public function __construct(string $dateFormat="Y-m-d H:i:s.u") {
        $this->dateFormat                           = $dateFormat;
    }

    /**
     * @param DateTime $value
     * @return string
     */
    public function getValue($value) {
        return $value->format($this->dateFormat);
    }
}