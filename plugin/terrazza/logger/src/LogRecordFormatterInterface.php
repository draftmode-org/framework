<?php

namespace Terrazza\Logger;

use Terrazza\Logger\Record\LogRecord;

interface LogRecordFormatterInterface {
    /**
     * @param string $tokenKey
     * @param LogRecordValueConverterInterface $valueConverter
     */
    public function pushConverter(string $tokenKey, LogRecordValueConverterInterface $valueConverter) : void;

    /**
     * @param LogRecord $record
     * @param array|null $format
     * @return array
     */
    public function formatRecord(LogRecord $record, array $format=null) : array;
}