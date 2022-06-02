<?php
namespace Terrazza\Http\Routing\OpenApi;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Terrazza\Http\Request\HttpRequestHandlerInterface;
use Terrazza\Http\Request\HttpRequestInterface;
use Terrazza\Http\Routing\HttpRouterInterface;
use Terrazza\Http\Routing\HttpRoutingInterface;

class OpenApiRouter implements HttpRouterInterface {
    private HttpRoutingInterface $routing;
    private ContainerInterface $injector;
    private LoggerInterface $logger;
    public function __construct(HttpRoutingInterface $routing, ContainerInterface $injector, LoggerInterface $logger) {
        $this->routing 								= $routing;
        $this->injector 				            = $injector;
        $this->logger 				                = $logger;
    }

    /**
     * @param HttpRequestInterface $request
     * @return HttpRequestHandlerInterface|null
     */
    function getRequestHandler(HttpRequestInterface $request): ?HttpRequestHandlerInterface {
        if ($route = $this->routing->getRoute($request->getUri(), $request->getMethod(), $request->getContentType())) {
            $this->logger->info("routing for uri: ".$request->getUri()." / method: ".$request->getMethod()." found");
            $controllerClass						= $this->injector->get($route->getOperationId());
            return new $controllerClass;
        } else {
            $this->logger->info("routing for uri: ".$request->getUri()." / method: ".$request->getMethod()." not found");
            return null;
        }
    }
}