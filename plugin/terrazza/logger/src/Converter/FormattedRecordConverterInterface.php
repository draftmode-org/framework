<?php

namespace Terrazza\Logger\Converter;

interface FormattedRecordConverterInterface {
    /**
     * @param array $data
     * @return string
     */
    public function convert(array $data) : string;
}