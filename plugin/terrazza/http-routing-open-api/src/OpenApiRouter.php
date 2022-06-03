<?php
namespace Terrazza\Http\Routing\OpenApi;

use Psr\Container\ContainerExceptionInterface;
use Terrazza\Http\Request\HttpRequestHandlerInterface;
use Terrazza\Http\Request\HttpRequestInterface;
use Terrazza\Http\Routing\HttpRouterInterface;
use Terrazza\Http\Routing\HttpRoutingInterface;
use Terrazza\Injector\InjectorInterface;
use Terrazza\Logger\LoggerInterface;

class OpenApiRouter implements HttpRouterInterface {
    private HttpRoutingInterface $routing;
    private InjectorInterface $injector;
    private LoggerInterface $logger;
    public function __construct(HttpRoutingInterface $routing, InjectorInterface $injector, LoggerInterface $logger) {
        $this->routing 								= $routing;
        $this->injector 				            = $injector;
        $this->logger 				                = $logger->withNamespace(__NAMESPACE__);
    }

    /**
     * @param HttpRequestInterface $request
     * @return HttpRequestHandlerInterface|null
     * @throws ContainerExceptionInterface
     */
    function getRequestHandler(HttpRequestInterface $request): ?HttpRequestHandlerInterface {
        $requestMethod                              = strtolower($request->getMethod());
        $requestPath                                = $request->getUri()->getPath();
        $requestContentType                         = $request->getContentType();
        $optionalRequestContentType                 = $requestContentType ? " / content-type: $requestContentType" : "";
        if ($route = $this->routing->getRoute($requestPath, $requestMethod, $requestContentType)) {
            $this->logger->info("routing for path: $requestPath / method: {$requestMethod}{$optionalRequestContentType} found");
            $requestHandlerClass                    = $route->getRequestHandlerClass();
            $controllerClass					    = $this->injector->get($requestHandlerClass);
            return new $controllerClass;
        } else {
            $this->logger->info("routing for path: $requestPath / method: {$requestMethod}{$optionalRequestContentType} found");
            return null;
        }
    }
}