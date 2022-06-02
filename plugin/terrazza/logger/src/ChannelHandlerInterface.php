<?php
namespace Terrazza\Logger;

use Terrazza\Logger\Record\LogRecord;

interface ChannelHandlerInterface {
    /**
     * @return LogWriterInterface
     */
    public function getWriter(): LogWriterInterface;

    /**
     * @return LogRecordFormatterInterface
     */
    public function getFormatter(): LogRecordFormatterInterface;

    /**
     * @return LogHandlerFilterInterface|null
     */
    public function getFilter(): ?LogHandlerFilterInterface;

    /**
     * @return LogHandlerInterface[]
     */
    public function getLogHandler() : array;

    /**
     * @param LogHandlerInterface $logHandler
     */
    public function pushLogHandler(LogHandlerInterface $logHandler) : void;

    /**
     * @param LogRecord $record
     * @return LogHandlerInterface|null
     */
    public function getEffectedHandler(LogRecord $record) :?LogHandlerInterface;

    /**
     * @param LogHandlerInterface $handler
     * @param LogRecord $record
     */
    public function writeRecord(LogHandlerInterface $handler, LogRecord $record): void;
}