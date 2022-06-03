<?php
namespace Terrazza\Http\Routing\OpenApi\Request;

use Terrazza\Http\Request\HttpRequestInterface;
use Terrazza\Http\Routing\HttpRequestValidatorInterface;
use Terrazza\Http\Routing\HttpRoute;
use Terrazza\Http\Routing\HttpRoutingInterface;
use Terrazza\Logger\LoggerInterface;

class OpenApiRequestValidator implements HttpRequestValidatorInterface {
    private HttpRoutingInterface $routing;
    private LoggerInterface $logger;
    public function __construct(HttpRoutingInterface $routing, LoggerInterface $logger) {
        $this->routing                              = $routing;
        $this->logger                               = $logger->withNamespace(__NAMESPACE__);
    }

    /**
     * @return HttpRoutingInterface
     */
    public function getRouting() : HttpRoutingInterface {
        return $this->routing;
    }

    /**
     * @param HttpRoute $route
     * @param HttpRequestInterface $request
     */
    public function validateParams(HttpRoute $route, HttpRequestInterface $request) : void {
        $config                                     = $this->routing->getConfig();
        $parameters                                 = $config->getPathParameters($route->getUri(), $route->getMethod());
        foreach (["query"] as $type) {
            if ($params = $parameters[$type] ?? null) {
                var_dump($params);
            }
        }
    }
}