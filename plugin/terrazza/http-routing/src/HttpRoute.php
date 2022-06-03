<?php
namespace Terrazza\Http\Routing;

class HttpRoute {
    private string $uri;
    private string $method;
    private string $requestHandlerClass;
    public function __construct(string $uri, string $method, string $requestHandlerClass) {
        $this->uri 									= $uri;
        $this->method 								= $method;
        $this->requestHandlerClass 					= $requestHandlerClass;
    }
    public function getUri() : string {
        return $this->uri;
    }
    public function getMethod() : string {
        return $this->method;
    }
    public function getRequestHandlerClass() : string {
        return $this->requestHandlerClass;
    }
}