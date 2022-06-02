<?php
namespace Terrazza\Http\Routing;
use Terrazza\Routing\RouteConfigInterface;

interface HttpRoutingInterface {
    public function getConfig() : RouteConfigInterface;
    public function getMatchedUri(string $requestUri) :?string;
    public function getRoute(string $requestUri, string $requestMethod, ?string $requestContentType=null) :?HttpRoute;
}