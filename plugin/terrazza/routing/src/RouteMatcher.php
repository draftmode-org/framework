<?php
namespace Terrazza\Routing;

class RouteMatcher implements RouteMatcherInterface {
    /**
     * @param array $findUris
     * @param string $requestUri
     * @return string|null
     */
    public function getRoutesMatchedUri(array $findUris, string $requestUri) :?string {
        $lastStaticUriLen						= 0;
        $matchedUri								= null;
        foreach ($findUris as $findUri) {
            if ($this->getRouteMatchUri($findUri, $requestUri)) {
                $staticUriLen                   = strpos($findUri, "{");
                $staticUriLen                   = ($staticUriLen === false) ? strlen($findUri) : $staticUriLen;
                if ($staticUriLen >= $lastStaticUriLen) {
                    $lastStaticUriLen			= $staticUriLen;
                    $matchedUri					= $findUri;
                }
            }
        }
        return $matchedUri;
    }

    /**
     * @param string $findUri
     * @param string $requestUri
     * @return bool
     */
    public function getRouteMatchUri(string $findUri, string $requestUri) : bool {
        $findUri 								= ltrim(rtrim($findUri, "/"), "/");
        $requestUri 							= ltrim(rtrim($requestUri, "/"), "/");

        $pattern                                = '^' . preg_replace('#\{[\w\_]+\}#', '(.+?)', $findUri);
        $pattern                                .= "$";
        if (preg_match("~".$pattern."~", $requestUri, $matches, PREG_OFFSET_CAPTURE)) {
            $lastMatch 										= array_pop($matches);
            $lastMatchedValue 								= $lastMatch[0];
            if (strpos($lastMatchedValue, "/")) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }
}