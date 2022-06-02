<?php
namespace Terrazza\Http\Routing;

use Terrazza\Http\Request\HttpRequestHandlerInterface;
use Terrazza\Http\Request\HttpRequestInterface;

interface HttpRouterInterface {
    /**
     * @param HttpRequestInterface $request
     * @return HttpRequestHandlerInterface|null
     */
    function getRequestHandler(HttpRequestInterface $request) :?HttpRequestHandlerInterface;
}