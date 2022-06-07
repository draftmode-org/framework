<?php
namespace Terrazza\Http\Routing;
use Terrazza\Http\Request\HttpRequestHandlerInterface;

interface HttpRequestHandlerBuilderInterface {
    /**
     * @param HttpRoute $route
     * @return HttpRequestHandlerInterface
     */
    public function getRequestHandler(HttpRoute $route) : HttpRequestHandlerInterface;
}