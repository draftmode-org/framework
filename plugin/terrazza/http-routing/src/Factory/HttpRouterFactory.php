<?php

namespace Terrazza\Http\Routing\Factory;

use Terrazza\Http\Request\HttpRequestInterface;
use Terrazza\Http\Routing\HttpRoute;
use Terrazza\Http\Routing\HttpRouteLoaderInterface;
use Terrazza\Http\Routing\HttpRouter;
use Terrazza\Http\Routing\HttpRouterInterface;
use Terrazza\Http\Routing\HttpRouting;
use Terrazza\Logger\LoggerInterface;
use Terrazza\Routing\RouteMatcher;

class HttpRouterFactory implements HttpRouterInterface {
    private HttpRouterInterface $router;
    public function __construct(HttpRouteLoaderInterface $loader, LoggerInterface $logger) {
        $this->router 								= new HttpRouter(
            new HttpRouting(new RouteMatcher(), $loader, $logger)
        );
    }

    /**
     * @param HttpRequestInterface $request
     * @return HttpRoute|null
     */
    public function getRoute(HttpRequestInterface $request): ?HttpRoute {
        return $this->router->getRoute($request);
    }
}