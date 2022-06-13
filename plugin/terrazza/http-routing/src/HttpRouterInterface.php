<?php
namespace Terrazza\Http\Routing;
use Terrazza\Http\Request\HttpRequestInterface;

interface HttpRouterInterface {
    /**
     * @param HttpRequestInterface $request
     * @return HttpRoute|null
     */
    function getRoute(HttpRequestInterface $request) :?HttpRoute;
}