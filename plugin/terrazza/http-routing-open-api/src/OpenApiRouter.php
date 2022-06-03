<?php
namespace Terrazza\Http\Routing\OpenApi;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function getRequestHandler(HttpRequestInterface $request): ?HttpRequestHandlerInterface {
        $requestMethod                              = strtolower($request->getMethod());
        $requestPath                                = $request->getUri()->getPath();
        $requestContentType                         = $request->getContentType();
        if ($route = $this->routing->getRoute($requestPath, $requestMethod, $requestContentType)) {
            $this->logger->info("routing for path: $requestPath / method: $requestMethod found");
            $controllerClass						= $this->injector->get($route->getOperationId());
            return new $controllerClass;
        } else {
            $this->logger->info("routing for path: $requestPath / method: $requestMethod found");
            return null;
        }
    }
}