<?php
namespace Terrazza\Http\Routing;

use Terrazza\Http\Routing\Exception\HttpMethodNotAllowedException;
use Terrazza\Http\Routing\Exception\HttpRouteNotFoundException;
use Terrazza\Http\Routing\Exception\HttpUnsupportedContentType;
use Terrazza\Routing\RouteMatcherInterface;

class HttpRouting implements HttpRoutingInterface {
    private HttpRouteLoaderInterface $loader;
    private RouteMatcherInterface $matcher;

    public function __construct(RouteMatcherInterface $matcher, HttpRouteLoaderInterface $loader) {
        $this->loader 								= $loader;
        $this->matcher 								= $matcher;
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
            return $this->matcher->getRoutesMatchedUri($paths, $requestPath);
        } else {
            return null;
        }
    }

    /**
     * @param string $requestPath
     * @param string $requestMethod
     * @param string|null $requestContentType
     * @return HttpRoute|null
     * @throws HttpUnsupportedContentType
     * @throws HttpMethodNotAllowedException
     * @throws HttpRouteNotFoundException
     */
    public function getRoute(string $requestPath, string $requestMethod, ?string $requestContentType=null) :?HttpRoute {
        if ($encodedPath = $this->getMatchedUri($requestPath)) {
            if ($this->loader->getPath($encodedPath, $requestMethod)) {
                // get contentTypes vai api.yaml requestBody
                if ($expectedContentTypes = $this->loader->getContentTypes($encodedPath, $requestMethod)) {

                    // match contentTypes from yaml against requestContentType
                    if ($this->contentTypeMatches($expectedContentTypes, $requestContentType)) {
                        $requestHandlerClass        = $this->loader->getRequestHandlerClass($encodedPath, $requestMethod, $requestContentType);
                        return new HttpRoute($encodedPath, $requestMethod, $requestHandlerClass);
                    } else {
                        throw new HttpUnsupportedContentType($requestContentType);
                    }
                } else {
                    // no contentTypes expected
                    $requestHandlerClass        = $this->loader->getRequestHandlerClass($encodedPath, $requestMethod, $requestContentType);
                    return new HttpRoute($encodedPath, $requestMethod, $requestHandlerClass);
                }
            } else {
                throw new HttpMethodNotAllowedException($requestMethod);
            }
        } else {
            throw new HttpRouteNotFoundException($requestPath);
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