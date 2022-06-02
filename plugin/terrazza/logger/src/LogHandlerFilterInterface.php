<?php
namespace Terrazza\Logger;

interface LogHandlerFilterInterface {
    public function isHandling(string $callerNamespace) : bool;
}