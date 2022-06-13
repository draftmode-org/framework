<?php
namespace Terrazza\Http\Routing\Exception;

class HttpMethodNotAllowedException extends HttpRouteNotFoundException {
    public function __construct(string $requestMethod) {
        parent::__construct("method $requestMethod unsupported", 405);
    }
}