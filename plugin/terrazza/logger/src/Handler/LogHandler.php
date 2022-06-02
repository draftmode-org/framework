<?php
namespace Terrazza\Logger\Handler;

use Terrazza\Logger\LogRecordFormatterInterface;
use Terrazza\Logger\Record\LogRecord;
use Terrazza\Logger\LogHandlerInterface;
use Terrazza\Logger\LogHandlerFilterInterface;

class LogHandler implements LogHandlerInterface {
    private int $logLevel;
    private ?array $format;
    private ?LogHandlerFilterInterface $filter;
    public function __construct(int $logLevel, ?array $format=null, ?LogHandlerFilterInterface $filter=null) {
        $this->logLevel 							= $logLevel;
        $this->format                               = $format;
        $this->filter                               = $filter;
    }

    /**
     * @return int
     */
    public function getLogLevel(): int {
        return $this->logLevel;
    }

    /**
     * @return array|null
     */
    public function getFormat() :?array {
        return $this->format;
    }

    /**
     * @return LogHandlerFilterInterface|null
     */
    public function getFilter() :?LogHandlerFilterInterface {
        return $this->filter;
    }

    /**
     * @param LogRecord $record
     * @return bool
     */
    public function isHandling(LogRecord $record) : bool {
        /*
         * record filter
         */
        if ($record->getLogLevel() >= $this->logLevel) {
            /**
             * Handler-/ChannelFilter
             */
            if ($this->filter) {
                return $this->filter->isHandling($record->getTrace()->getNamespace());
            } else {
                return true;
            }
        }
        return false;
    }
}