<?php

namespace Terrazza\Logger;

class LogHandlerFilter implements LogHandlerFilterInterface {
    private ?array $include;
    private ?array $exclude;
    private ?array $start;
    private bool $isStarted=false;

    public function __construct(?array $include=null, ?array $exclude=null, ?array $start=null) {
        $this->include                              = $include;
        $this->exclude                              = $exclude;
        $this->start                                = $start;
    }

    public function isHandling(string $callerNamespace) : bool {
        /**
         * performance issue
         */
        if (!$this->include &&
            !$this->exclude &&
            !$this->start) {
            return true;
        }
        $namespace                                  = $this->escapeSlashes($callerNamespace, 1);
        if ($this->exclude && $this->preg_match($namespace, $this->exclude)) {
            return false;
        }
        if ($this->start && $this->isStarted) {
            return true;
        }
        if ($this->start && $this->preg_match($namespace, $this->start)) {
            $this->isStarted                        = true;
        }
        if ($this->include && !$this->preg_match($namespace, $this->include)) {
            return false;
        }
        if ($this->start && !$this->isStarted) {
            return false;
        }
        return true;
    }

    /**
     * @param string $text
     * @param int $repeat
     * @return string
     */
    private function escapeSlashes(string $text, int $repeat) : string {
        return strtr($text, [
            "\\"                                    => str_repeat("\\\\", $repeat),
            "/"                                     => str_repeat("\\\\", $repeat)
        ]);
    }

    /**
     * @param string $namespace
     * @param array $filter
     * @return bool
     */
    private function preg_match(string $namespace, array $filter) : bool {
        $pattern                                    = "#(".join(")|(", $filter).")#";
        $pattern                                    = $this->escapeSlashes($pattern, 2);
        return (preg_match($pattern, $namespace) === 1);
    }
}