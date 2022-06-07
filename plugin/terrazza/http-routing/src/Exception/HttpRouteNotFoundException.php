<?php
namespace Terrazza\Http\Routing\Exception;
use InvalidArgumentException;

class HttpRouteNotFoundException extends InvalidArgumentException {
    public function __construct(string $requestUri) {
        parent::__construct("requested uri $requestUri deceptive", 400);
    }
}