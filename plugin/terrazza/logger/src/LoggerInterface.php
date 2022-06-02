<?php
namespace Terrazza\Logger;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Throwable;

interface LoggerInterface extends PsrLoggerInterface {
    public function registerExceptionHandler() : void;
    /**
     * @param Throwable $exception
     */
    public function handleException(Throwable $exception) : void;

    /**
     * @param int $errorTypes
     */
    public function registerErrorHandler(int $errorTypes = -1) : void;

    /**
     * @param int $display_errors
     */
    public function registerFatalHandler(int $display_errors=0) : void;
    public function handleFatalError() : void;

    /**
     * @param string $exceptionFileName
     */
    public function setExceptionFileName(string $exceptionFileName) : void;

    /**
     * @param ChannelHandlerInterface $channelHandler
     * @return LoggerInterface
     */
    public function registerChannelHandler(ChannelHandlerInterface $channelHandler) : LoggerInterface;
}