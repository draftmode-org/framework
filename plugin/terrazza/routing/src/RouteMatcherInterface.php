<?php
namespace Terrazza\Routing;

interface RouteMatcherInterface {
    public function getRoutesMatchedUri(array $findUris, string $requestUri) :?string;
    public function getRouteMatchUri(string $findUri, string $requestUri) : bool;
}