<?php
namespace Terrazza\Http\Routing;
use Terrazza\Routing\RouteConfigInterface;

interface HttpRoutingInterface {
    /**
     * @return RouteConfigInterface
     */
    public function getConfig() : RouteConfigInterface;

    /**
     * @param string $requestPath
     * @return string|null
     */
    public function getMatchedUri(string $requestPath) :?string;

    /**
     * @param string $requestPath
     * @param string $requestMethod
     * @param string|null $requestContentType
     * @return HttpRoute|null
     */
    public function getRoute(string $requestPath, string $requestMethod, ?string $requestContentType=null) :?HttpRoute;
}