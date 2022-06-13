<?php
namespace Terrazza\Http\Routing\Exception;

class HttpUriNotFoundException extends HttpRouteNotFoundException {
    public function __construct(string $requestUri) {
        parent::__construct("requested uri $requestUri deceptive", 404);
    }
}