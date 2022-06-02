<?php
namespace Terrazza\Logger\Factory\Writer;

use Terrazza\Logger\Converter\FormattedRecord\FormattedRecordFlatConverter;
use Terrazza\Logger\LogWriterInterface;
use Terrazza\Logger\Writer\LogStreamFileWriter;

class FlatStdOut implements LogWriterInterface {
    private LogWriterInterface $writer;

    public function __construct(string $delimiter=" ") {
        $converter                                  = new FormattedRecordFlatConverter($delimiter);
        $this->writer                               = new LogStreamFileWriter($converter, "php://stdout");
    }

    public function write(array $record): void {
        $this->writer->write($record);
    }
}