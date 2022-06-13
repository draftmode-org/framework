<?php
namespace Terrazza\Kernel\Helper;

use Terrazza\Logger\Handler\ChannelHandler;
use Terrazza\Logger\Handler\LogHandler;
use Terrazza\Logger\Converter\FormattedRecord\FormattedRecordFlatConverter;
use Terrazza\Logger\Converter\NonScalar\NonScalarJsonConverter;
use Terrazza\Logger\Formatter\LogRecordFormatter;
use Terrazza\Logger\LoggerInterface;
use Terrazza\Logger\Logger as rLogger;
use Terrazza\Logger\Utility\RecordValueConverter\LogRecordValueDateConverter;
use Terrazza\Logger\Utility\RecordValueConverter\LogRecordValueExceptionConverter;
use Terrazza\Logger\Writer\LogStreamFileWriter;

class LoggerHelper {
    private string $name;
    public function __construct(string $name) {
        $this->name = $name;
    }

    public function createLogger(int $logLevel, $stream=null) : LoggerInterface {
        $logger                                     = new rLogger($this->name);
        $format                                     = [
            "message"   => "{Date} [{LevelName}] {Trace.Method} (#{Trace.Line}) {Message} {Context}",
            "exception" => "{Context.exception}"
        ];
        if ($stream === true) {
            $stream                                 = "php://stdout";
        }
        if (is_string($stream)) {
            $formatter                              = new LogRecordFormatter(new NonScalarJsonConverter(), $format);
            $formatter->pushConverter("Date", new LogRecordValueDateConverter());
            $formatter->pushConverter("Context.exception", new LogRecordValueExceptionConverter());
            $writer                                 = new LogStreamFileWriter(new FormattedRecordFlatConverter(" "), $stream, FILE_APPEND);
            @unlink($stream);
            $channelHandler                           = new ChannelHandler($writer, $formatter, null, new LogHandler($logLevel));
            return $logger->registerChannelHandler($channelHandler);
        } elseif ($stream === false) {
            return $logger;
        } else {
            return $logger;
        }
    }
}