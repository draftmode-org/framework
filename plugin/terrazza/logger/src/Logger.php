<?php
namespace Terrazza\Logger;

use Terrazza\Logger\Record\LogRecord;
use Terrazza\Logger\Record\LogRecordTrace;
use Throwable;

class Logger implements LoggerInterface {
    public const LOG                                = 50;
    public const DEBUG                              = 100;
    public const INFO                               = 200;
    public const NOTICE                             = 250;
    public const WARNING                            = 300;
    public const ERROR                              = 400;
    public const CRITICAL                           = 500;
    public const ALERT                              = 550;
    public const EMERGENCY                          = 600;

    public static array $levels = [
        self::LOG                                   => 'LOG',
        self::DEBUG                                 => 'DEBUG',
        self::INFO                                  => 'INFO',
        self::NOTICE                                => 'NOTICE',
        self::WARNING                               => 'WARNING',
        self::ERROR                                 => 'ERROR',
        self::CRITICAL                              => 'CRITICAL',
        self::ALERT                                 => 'ALERT',
        self::EMERGENCY                             => 'EMERGENCY'
    ];

    private string $loggerName;
    private array $context;
    /**
     * @var ChannelHandlerInterface[]
     */
    private array $channelHandler                   = [];

    /**
     * @var string
     */
    private string $exceptionFileName               = "php://stderr";

    /**
     * @param string $loggerName
     * @param array|null $context
     * @param ChannelHandlerInterface ...$channelHandler
     */
    public function __construct(string $loggerName, array $context=null, ChannelHandlerInterface ...$channelHandler) {
        $this->loggerName                           = $loggerName;
        $this->context                              = $context ?? [];
        $this->channelHandler                       = $channelHandler;
    }

    /**
     * @param ChannelHandlerInterface $channelHandler
     * @return LoggerInterface
     */
    public function registerChannelHandler(ChannelHandlerInterface $channelHandler) : LoggerInterface {
        $this->channelHandler[]                     = $channelHandler;
        return $this;
    }

    /**
     *
     */
    public function registerExceptionHandler() : void {
        set_exception_handler([$this, "handleException"]);
    }

    /**
     * @param int $errorTypes
     */
    public function registerErrorHandler(int $errorTypes = E_ALL) : void {
        set_error_handler([$this, "handleError"], $errorTypes);
    }

    /**
     * @param int $display_errors
     */
    public function registerFatalHandler(int $display_errors=0) : void {
        ini_set('display_errors',"$display_errors");
        register_shutdown_function([$this, "handleFatalError"]);
    }

    /**
     * @param string $exceptionFileName
     */
    public function setExceptionFileName(string $exceptionFileName) : void {
        $this->exceptionFileName                    = $exceptionFileName;
    }

    /**
     *
     */
    public function handleFatalError() : void {
        if ($error = error_get_last()) {
            $this->handleError(
                $error["type"],
                $error["message"],
                $error["file"],
                $error["line"]
            );
        }
    }

    /**
     * @param int $errorCode
     * @param string $message
     * @param string|null $file
     * @param int|null $line
     */
    public function handleError(int $errorCode, string $message, string $file=null, int $line=null) : void {
        $context                                    = [
            "file"                                  => $file,
            "line"                                  => $line,
        ];
        switch ($errorCode) {
            case E_USER_NOTICE:
            case E_NOTICE:
                $this->notice($message, $context);
                break;
            case E_USER_WARNING:
            case E_WARNING:
                $this->warning($message, $context);
                break;
            default:
                $this->emergency("$message (#$errorCode) {$file} {$line}", $context);
        }
    }

    /**
     * @param Throwable $exception
     */
    public function handleException(Throwable $exception) : void {
        $this->emergency("application exception: ".$exception->getMessage());//, ["exception" => $exception]);
    }

    /**
     * @param int $shifts
     * @return LogRecordTrace
     */
    private function getTrace(int $shifts=2) : LogRecordTrace {
        $traces                                     = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        for($shift=0;$shift < $shifts;$shift++) {
            array_shift($traces);
        }
        $trace                                      = array_shift($traces);
        $line                                       = $trace["line"] ?? null;
        //
        $trace                                      = array_shift($traces);
        $class                                      = $trace["class"] ?? "-";
        $function                                   = $trace["function"] ?? "-";
        $namespace                                  = join("\\", array_slice(explode("\\", $class), 0, -1));
        return new LogRecordTrace(
            $namespace,
            $class,
            $function,
            $line
        );
    }

    /**
     * @param int $logLevel
     * @param string $message
     * @param array $context
     */
    private function addMessage(int $logLevel, string $message, array $context=[]) : void {
        //
        $record = LogRecord::createRecord(
            $this->loggerName,
            $logLevel,
            $message,
            $this->getTrace(),
            $context,
            $this->context,
        );

        try {
            foreach ($this->channelHandler as $channelHandler) {
                if ($logHandler = $channelHandler->getEffectedHandler($record)) {
                    $channelHandler->writeRecord($logHandler, $record);
                }
            }
        } catch (Throwable $exception) {
            file_put_contents($this->exceptionFileName, join(" ", [
                (new \DateTime())->format("Y-m-d H:i:s.u"),
                "[".self::$levels[self::EMERGENCY]."]",
                $exception->getMessage().PHP_EOL.
                $exception->getTraceAsString()
            ]), FILE_APPEND);
        }
    }

    public function emergency($message, array $context = array()) : void {
        $this->addMessage(self::EMERGENCY, $message, $context);
    }

    public function alert($message, array $context = array()) : void {
        $this->addMessage(self::ALERT, $message, $context);
    }

    public function critical($message, array $context = array()) : void  {
        $this->addMessage(self::CRITICAL, $message, $context);
    }

    public function error($message, array $context = array()) : void  {
        $this->addMessage(self::ERROR,$message, $context);
    }

    public function warning($message, array $context = array()) : void  {
        $this->addMessage(self::WARNING, $message, $context);
    }

    public function notice($message, array $context = array()) : void  {
        $this->addMessage(self::NOTICE, $message, $context);
    }

    public function info($message, array $context = array()) : void  {
        $this->addMessage(self::INFO, $message, $context);
    }

    public function debug($message, array $context = array()) : void  {
        $this->addMessage(self::DEBUG, $message, $context);
    }

    public function log($level, $message, array $context = array()) : void  {
        $this->addMessage(self::LOG, $message, $context);
    }
}