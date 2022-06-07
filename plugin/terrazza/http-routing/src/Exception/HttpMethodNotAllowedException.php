<?php
namespace Terrazza\Http\Routing\Exception;
use InvalidArgumentException;

class HttpMethodNotAllowedException extends InvalidArgumentException {
    public function __construct(string $requestMethod) {
        parent::__construct("method $requestMethod unsupported", 405);
    }
}