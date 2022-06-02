<?php
namespace Terrazza\Http\Routing\OpenApi;

use Terrazza\Http\Routing\HttpRoute;
use Terrazza\Http\Routing\HttpRoutingInterface;
use Terrazza\Routing\RouteConfigInterface;
use Terrazza\Routing\RouteMatcherInterface;

use Terrazza\Framework\Controller\Http\HttpPaymentGetController;

class OpenApiRouting implements HttpRoutingInterface {
    private RouteConfigInterface $config;
    private RouteMatcherInterface $matcher;

    public function __construct(RouteMatcherInterface $matcher, RouteConfigInterface $config) {
        $this->config 								= $config;
        $this->matcher 								= $matcher;
    }

    /**
     * @return RouteConfigInterface
     */
    public function getConfig() : RouteConfigInterface {
        return $this->config;
    }

    /**
     * @param string $requestUri
     * @return string|null
     */
    public function getMatchedUri(string $requestUri) :?string {
        if ($paths = $this->config->getPaths()) {
            return $this->matcher->getRoutesMatchedUri($paths, $requestUri);
        } else {
            return null;
        }
    }

    /**
     * @param string $requestUri
     * @param string $requestMethod
     * @param string|null $requestContentType
     * @return HttpRoute|null
     */
    public function getRoute(string $requestUri, string $requestMethod, ?string $requestContentType=null) :?HttpRoute {
        if ($uri = $this->getMatchedUri($requestUri)) {
            if ($path = $this->config->getPath($uri, $requestMethod)) {

                // get contentTypes vai api.yaml requestBody
                if ($expectedContentTypes = $this->config->getContentTypes($uri, $requestMethod)) {

                    // match contentTypes from yaml against requestContentType
                    if ($this->contentTypeMatches($expectedContentTypes, $requestContentType)) {
                        return new HttpRoute($uri, $requestMethod, HttpPaymentGetController::class);
                    } else {
                        var_dump("content type failure", $expectedContentTypes);
                    }
                } else {
                    // no contentTypes expected
                    return new HttpRoute($uri, $requestMethod, HttpPaymentGetController::class);
                }
            } else {
                var_dump("uri found, method not found");
            }
        } else {
            var_dump("uri not found");
        }
        return null;
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