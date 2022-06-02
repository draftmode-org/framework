<?php
namespace Terrazza\Logger;

interface ChannelInterface {
    /**
     * @return string
     */
    public function getName() : string;

    /**
     * @return LogRecordFormatterInterface
     */
    public function getFormatter() : LogRecordFormatterInterface;

    /**
     * @return LogHandlerFilterInterface|null
     */
    public function getFilter() : ?LogHandlerFilterInterface;

    /**
     * @return LogWriterInterface
     */
    public function getWriter() : LogWriterInterface;
}