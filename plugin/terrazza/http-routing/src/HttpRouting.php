<?php
namespace Terrazza\Http\Routing;

use Terrazza\Http\Routing\Exception\HttpMethodNotAllowedException;
use Terrazza\Http\Routing\Exception\HttpUnsupportedContentType;
use Terrazza\Http\Routing\Exception\HttpUriNotFoundException;
use Terrazza\Logger\LoggerInterface;
use Terrazza\Routing\RouteMatcherInterface;

class HttpRouting implements HttpRoutingInterface {
    private HttpRouteLoaderInterface $loader;
    private RouteMatcherInterface $matcher;
    private LoggerInterface $logger;

    public function __construct(RouteMatcherInterface $matcher, HttpRouteLoaderInterface $loader, LoggerInterface $logger) {
        $this->loader 								= $loader;
        $this->matcher 								= $matcher;
        $this->logger                               = $logger->withNamespace(__NAMESPACE__);
    }

    /**
     * @return HttpRouteLoaderInterface
     */
    public function getLoader() : HttpRouteLoaderInterface {
        return $this->loader;
    }

    /**
     * @param string $requestPath
     * @return string|null
     */
    public function getMatchedUri(string $requestPath) :?string {
        if ($paths = $this->loader->getPaths()) {
            $this->logger->debug("loader path count: ".count($paths));
            return $this->matcher->getRoutesMatchedUri($paths, $requestPath);
        } else {
            $this->logger->debug("no loader paths found");
            return null;
        }
    }

    /**
     * @param string $requestPath
     * @param string $requestMethod
     * @param string|null $requestContentType
     * @return HttpRoute
     * @throws HttpUnsupportedContentType
     * @throws HttpMethodNotAllowedException
     * @throws HttpUriNotFoundException
     */
    public function getRoute(string $requestPath, string $requestMethod, ?string $requestContentType=null) : HttpRoute {
        if ($encodedPath = $this->getMatchedUri($requestPath)) {
            if ($this->loader->getPath($encodedPath, $requestMethod)) {
                // get contentTypes (optional)
                if ($expectedContentTypes = $this->loader->getContentTypes($encodedPath, $requestMethod)) {
                    $this->logger->debug("expected content-types for uri/method provided");
                    // match contentTypes from yaml against requestContentType
                    if ($this->contentTypeMatches($expectedContentTypes, $requestContentType)) {
                        $this->logger->debug("content-type matched expected ones");
                        $requestHandlerClass        = $this->loader->getRequestHandlerClass($encodedPath, $requestMethod, $requestContentType);
                        return new HttpRoute($encodedPath, $requestMethod, $requestHandlerClass);
                    } else {
                        $this->logger->debug("content-type not accepted, given ".$requestContentType);
                        throw new HttpUnsupportedContentType($requestContentType);
                    }
                } else {
                    $this->logger->debug("content-type for uri/method not restricted");
                    // no contentTypes expected
                    $requestHandlerClass        = $this->loader->getRequestHandlerClass($encodedPath, $requestMethod, $requestContentType);
                    return new HttpRoute($encodedPath, $requestMethod, $requestHandlerClass);
                }
            } else {
                $this->logger->debug("method $requestMethod for uri $requestPath not found");
                throw new HttpMethodNotAllowedException($requestMethod);
            }
        } else {
            $this->logger->debug("uri $requestPath not found in given paths");
            throw new HttpUriNotFoundException($requestPath);
        }
    }

    /**
     * @param array $expectedContentTypes
     * @param string|null $requestContentType
     * @return bool
     */
    private function contentTypeMatches(array $expectedContentTypes, ?string $requestContentType=null) : bool {
        if (count($expectedContentTypes)) {
            // no contentType given, but only one expected
            if (!$requestContentType && count($expectedContentTypes) === 1) {
                return true;
            }
            if (in_array($requestContentType, $expectedContentTypes)) {
                return true;
            }
        }
        return false;
    }
}