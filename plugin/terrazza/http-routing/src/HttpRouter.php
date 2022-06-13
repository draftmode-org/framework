<?php
namespace Terrazza\Http\Routing;

use Terrazza\Http\Request\HttpRequestInterface;
use Terrazza\Http\Routing\Exception\HttpMethodNotAllowedException;
use Terrazza\Http\Routing\Exception\HttpRouteNotFoundException;
use Terrazza\Http\Routing\Exception\HttpUnsupportedContentType;

class HttpRouter implements HttpRouterInterface {
    private HttpRoutingInterface $routing;
    public function __construct(HttpRoutingInterface $routing) {
        $this->routing 								= $routing;
    }

    /**
     * @param HttpRequestInterface $request
     * @return HttpRoute
     * @throws HttpUnsupportedContentType
     * @throws HttpMethodNotAllowedException
     * @throws HttpRouteNotFoundException
     */
    function getRoute(HttpRequestInterface $request) : HttpRoute {
        $requestMethod                              = strtolower($request->getMethod());
        $requestPath                                = $request->getUri()->getPath();
        $requestContentType                         = $request->getContentType();
        return $this->routing->getRoute($requestPath, $requestMethod, $requestContentType);
    }
}