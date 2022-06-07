<?php
namespace Terrazza\Http\Routing;

use Psr\Container\ContainerExceptionInterface;
use Terrazza\Http\Request\HttpRequestHandlerInterface;
use Terrazza\Http\Request\HttpRequestInterface;
use Terrazza\Injector\InjectorInterface;
use Terrazza\Logger\LoggerInterface;

class HttpRouter implements HttpRouterInterface {
    private HttpRoutingInterface $routing;
    private HttpRequestHandlerBuilderInterface $requestHandlerBuilder;
    private LoggerInterface $logger;
    public function __construct(HttpRoutingInterface $routing, HttpRequestHandlerBuilderInterface $requestHandlerBuilder, InjectorInterface $injector, LoggerInterface $logger) {
        $this->routing 								= $routing;
        $this->requestHandlerBuilder                = $requestHandlerBuilder;
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
        $optionalRequestContentType                 = $requestContentType ? ", content-type: $requestContentType" : "";

        // find route
        $route                                      = $this->routing->getRoute($requestPath, $requestMethod, $requestContentType);
        $this->logger->info("$requestPath, method: {$requestMethod}{$optionalRequestContentType} found");

        // buildRequestHandler
        return $this->requestHandlerBuilder->getRequestHandler($route);
    }
}