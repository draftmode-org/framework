<?php

namespace Terrazza\Logger\Converter;

interface NonScalarConverterInterface {
    /**
     * @param string $tKey
     * @param object|array $content
     * @return string
     */
    public function getValue(string $tKey, $content) : string;
}