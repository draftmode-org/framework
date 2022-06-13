<?php
namespace Terrazza\Http\Routing;

use Terrazza\Http\Routing\Exception\HttpMethodNotAllowedException;
use Terrazza\Http\Routing\Exception\HttpUnsupportedContentType;
use Terrazza\Http\Routing\Exception\HttpUriNotFoundException;

interface HttpRoutingInterface {
    /**
     * @return HttpRouteLoaderInterface
     */
    public function getLoader() : HttpRouteLoaderInterface;

    /**
     * @param string $requestPath
     * @return string|null
     */
    public function getMatchedUri(string $requestPath) :?string;

    /**
     * @param string $requestPath
     * @param string $requestMethod
     * @param string|null $requestContentType
     * @return HttpRoute
     * @throws HttpUnsupportedContentType
     * @throws HttpMethodNotAllowedException
     * @throws HttpUriNotFoundException
     */
    public function getRoute(string $requestPath, string $requestMethod, ?string $requestContentType=null) : HttpRoute;
}