<?php
namespace Terrazza\Http\Routing;

class HttpRoute {
    private string $uri;
    private string $method;
    private string $operationId;
    public function __construct(string $uri, string $method, string $operationId) {
        $this->uri 									= $uri;
        $this->method 								= $method;
        $this->operationId 							= $operationId;
    }
    public function getUri() : string {
        return $this->uri;
    }
    public function getMethod() : string {
        return $this->method;
    }
    public function getOperationId() : string {
        return $this->operationId;
    }
}