<?php
namespace Terrazza\Http\Routing\Exception;
use RuntimeException;
use Throwable;

class HttpRoutingControllerException extends RuntimeException {
    public function __construct(string $class, ?Throwable $previous=null) {
        parent::__construct("httpRoutingController $class does not exist", 500, $previous);
    }
}