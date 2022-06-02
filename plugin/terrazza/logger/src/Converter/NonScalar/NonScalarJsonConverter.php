<?php
namespace Terrazza\Logger\Converter\NonScalar;
use Terrazza\Logger\Converter\NonScalarConverterInterface;

class NonScalarJsonConverter implements NonScalarConverterInterface {
    /**
     * @param string $tKey
     * @param array|object $content
     * @return string
     */
    public function getValue(string $tKey, $content) : string {
        return $tKey.":".json_encode($content);
    }
}