<?php
namespace Terrazza\Logger\Writer;
use RuntimeException;

class LogWriterException extends RuntimeException {
    public function __construct(string $path) {
        parent::__construct(sprintf('cannot write/connect to "%s"', $path));
    }
}