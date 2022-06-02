<?php
namespace Terrazza\Logger\Converter\FormattedRecord;
use Terrazza\Logger\Converter\FormattedRecordConverterInterface;

class FormattedRecordFlatConverter implements FormattedRecordConverterInterface {
    private string $delimiter;
    private ?string $nonScalarPrefix=null;
    private int $encodingFlags;
    public function __construct(string $delimiter, int $encodingFlags=0) {
        $this->delimiter                            = $delimiter;
        $this->encodingFlags                        = $encodingFlags;
    }

    public function setNonScalarPrefix(string $delimiter) : void {
        $this->nonScalarPrefix                      = $delimiter;
    }

    /**
     * @param array $data
     * @return string
     */
    public function convert(array $data) : string {
        $lines										= [];
        foreach ($data as $dKey => $dValue) {
            if (is_scalar($dValue) || is_null($dValue)) {
                $lines[]							= $dValue;
            }
            else {
                $content                            = "";
                if ($this->nonScalarPrefix) {
                    $content                        .= $dKey . $this->nonScalarPrefix;
                }
                $content                            .= json_encode($dValue, $this->encodingFlags);
                $lines[] 							= $content;
            }
        }
        return implode($this->delimiter, $lines);
    }
}