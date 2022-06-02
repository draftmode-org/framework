<?php

namespace Terrazza\Logger\Utility\RecordValueConverter;
use Terrazza\Logger\LogRecordValueConverterInterface;
use Throwable;

class LogRecordValueExceptionConverter implements LogRecordValueConverterInterface {
    private int $traceMessageMax;
    private bool $traceMessageArgs;
    private int $cTraceMessage                      = 0;

    public function __construct(int $traceMessageMax=0, bool $traceMessageArgs=false) {
        $this->traceMessageMax                      = $traceMessageMax;
        $this->traceMessageArgs                     = $traceMessageArgs;
        $this->cTraceMessage                        = 0;
    }

    /**
     * @param array $trace
     * @param int $iTrace
     * @param int $tLevel
     * @return array
     */
    private function getTraceMessage(array $trace, int $iTrace, int $tLevel) : array {
        $line                                       = "-";
        $file                                       = "-";
        $function                                   = "-";
        if (array_key_exists($iTrace, $trace)) {
            $line                                   = $trace[$iTrace]["line"] ?? "-";
            $file                                   = $trace[$iTrace]["file"] ?? "-";
            $function                               = $trace[$iTrace]["function"] ?? "-";
        }
        $nClass                                     = "-";
        $nFunction                                  = "-";
        if (array_key_exists($iTrace+1, $trace)) {
            $nClass                                 = $trace[$iTrace+1]["class"] ?? "-";
            $nFunction                              = $trace[$iTrace+1]["function"] ?? "-";
        }
        $message = [
            "file"                                  => $file,
            "line"                                  => $line,
            "class"                                 => $nClass,
            "method"                                => $nFunction,
            "tLevel"                                => $tLevel
        ];
        if ($this->traceMessageArgs && array_key_exists("args", $trace[$iTrace])) {
            $args                                   = $trace[$iTrace]["args"];
            if ($function === "__invoke" && is_array($args)) {
                $in_array_file                      = $args[2] ?? "-";
                $in_array_line                      = $args[3] ?? "-";
                $in_array_args                      = $args[4] ?? "-";
                if (is_array($in_array_args) &&
                    $in_array_file === $file &&
                    $in_array_line === $line
                ) {
                    $args                           = $in_array_args;
                }
            }
            if (count($args)) {
                $message["args"]                    = $args;
            }
        }
        return $message;
    }

    /**
     * @param Throwable $exception
     * @param int $tLevel
     * @return array
     */
    private function getFirstMessage(Throwable $exception, int $tLevel) : array {
        return [
            "message"                           => $exception->getMessage(),
            "class"                             => get_class($exception),
            "file"                              => $exception->getFile(),
            "line"                              => $exception->getLine(),
            "tLevel"                            => $tLevel,
        ];
    }

    /**
     * @param Throwable $exception
     * @param int $tLevel
     * @param array|null $parentMessage
     * @return array
     */
    private function convertTrace(Throwable $exception, int $tLevel, array $parentMessage=null) : array {
        $traces                                     = $exception->getTrace();
        $messages                                   = [];
        for ($iTrace=0; $iTrace < count($traces); $iTrace++) {
            $message                                = $this->getTraceMessage($traces, $iTrace, $tLevel);
            if ($parentMessage &&
                $parentMessage["file"] === $message["file"] &&
                $parentMessage["line"] === $message["line"]
            ) {
                break;
            }
            if ($this->traceMessageMax && $this->cTraceMessage == $this->traceMessageMax) break;
            $messages[]                             = $message;
            $this->cTraceMessage++;
            if ($iTrace === 0 && $previous = $exception->getPrevious()) {
                $tLevel++;
                $pMessages                          = $this->convertTrace($previous, $tLevel, $message);
                if (count($pMessages)) {
                    arsort($pMessages);
                    $messages                       = array_merge($messages, $pMessages);
                }
                break;
            }
        }
        return $messages;
    }

    private function getExceptionMessages(Throwable $exception, int $tLevel) : array {
        $messages                                   = [];
        $messages[]                                 = $this->getFirstMessage($exception, $tLevel);
        while ($previous = $exception->getPrevious()) {
            $messages[]                             = $this->getFirstMessage($previous, ++$tLevel);
            $exception                              = $previous;
        }
        return $messages;
    }

    /**
     * @param Throwable $value
     * @return array
     */
    public function getValue($value) {
        return [
            "message"                               => $this->getExceptionMessages($value, 1),
            "trace"                                 => $this->convertTrace($value, 1),
        ];
    }
}