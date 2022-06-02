<?php

namespace Terrazza\Logger\Record;
use DateTime;
use Terrazza\Logger\Logger;

class LogRecord {
    private \DateTime $logDate;
    private string $loggerName;
    private int $logLevel;
    private string $logMessage;
    private LogRecordTrace $trace;
    private array $context;
    private array $initContext;
    private float $memUsed;
    private float $memAllocated;

    /**
     * @param DateTime $logDate
     * @param string $loggerName
     * @param int $logLevel
     * @param string $logMessage
     * @param float $memUsed
     * @param float $memAllocated
     * @param LogRecordTrace $trace
     * @param array|null $context
     * @param array|null $initContext
     */
    public function __construct(DateTime $logDate,
                                string $loggerName,
                                int $logLevel,
                                string $logMessage,
                                float $memUsed,
                                float $memAllocated,
                                LogRecordTrace $trace,
                                array $context=null,
                                array $initContext=null)
    {
        $this->logDate                              = $logDate;
        $this->loggerName                           = $loggerName;
        $this->logLevel                             = $logLevel;
        $this->logMessage                           = $logMessage;
        $this->memUsed                           	= $memUsed;
        $this->memAllocated                         = $memAllocated;
        $this->trace                                = $trace;
        $this->context                              = $context ?? [];
        $this->initContext                          = $initContext ?? [];
    }

    /**
     * @param string $loggerName
     * @param int $logLevel
     * @param string $logMessage
     * @param LogRecordTrace $trace
     * @param array|null $context
     * @param array|null $initContext
     * @return LogRecord
     */
    public static function createRecord(string $loggerName,
                                        int $logLevel,
                                        string $logMessage,
                                        LogRecordTrace $trace,
                                        array $context=null,
                                        array $initContext=null): LogRecord {
        return new self(
            new DateTime(),
            $loggerName,
            $logLevel,
            $logMessage,
            round(memory_get_usage(false) / 1024),
            round(memory_get_usage(true) / 1024),
            $trace,
            $context,
            $initContext
        );
    }

    /**
     * @return DateTime
     */
    public function getLogDate(): DateTime
    {
        return $this->logDate;
    }

    /**
     * @return string
     */
    public function getLoggerName(): string
    {
        return $this->loggerName;
    }

    /**
     * @return int
     */
    public function getLogLevel(): int
    {
        return $this->logLevel;
    }


    /**
     * @return string
     */
    public function getLogLevelName(): string
    {
        return Logger::$levels[$this->logLevel];
    }

    /**
     * @return string
     */
    public function getLogMessage(): string
    {
        return $this->logMessage;
    }

    /**
     * @return float
     */
    public function getMemUsed() : float {
        return $this->memUsed;
    }

    /**
     * @return float
     */
    public function getMemAllocated() : float {
        return $this->memAllocated;
    }

    /**
     * @return LogRecordTrace
     */
    public function getTrace() : LogRecordTrace {
        return $this->trace;
    }

    /**
     * @return array
     */
    public function getContext() : array {
        return $this->context;
    }

    /**
     * @return array
     */
    public function getInitContext() : array {
        return $this->initContext;
    }

    /**
     * @return array
     */
    public function getToken() : array {
        return [
            'Date' 				                    => $this->getLogDate(),
            'Level' 			                    => $this->getLogLevel(),
            'LevelName'     	                    => $this->getLogLevelName(),
            'LoggerName' 		                    => $this->getLoggerName(),
            'MemUsed'			                    => $this->getMemUsed(),
            'MemAllocated'		                    => $this->getMemAllocated(),
            'Message' 			                    => $this->getLogMessage(),
            'Context'			                    => $this->getContext(),
            'iContext'			                    => $this->getInitContext(),
            'Trace'			                        => [
                "Namespace"                         => $this->getTrace()->getNamespace(),
                "Line"                              => $this->getTrace()->getLine(),
                "Classname"                         => $this->getTrace()->getClassname(),
                "Function"                          => $this->getTrace()->getFunction(),
                "Method"                            => $this->getTrace()->getClassname()."::".$this->getTrace()->getFunction(),
                "sMethod"                           => basename($this->getTrace()->getClassname())."::".$this->getTrace()->getFunction(),
            ]
        ];
    }
}