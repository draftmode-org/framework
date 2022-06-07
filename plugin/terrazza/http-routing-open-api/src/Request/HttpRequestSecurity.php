<?php
namespace Terrazza\Http\Routing\OpenApi\Request;
use Terrazza\Http\Routing\HttpRequestSecurityInterface;
use Terrazza\Http\Routing\HttpRoutingInterface;

class HttpRequestSecurity implements HttpRequestSecurityInterface {
    private HttpRoutingInterface $routing;
    public function __construct(HttpRoutingInterface $routing) {
        $this->routing                              = $routing;
    }

    /**
     * @return HttpRoutingInterface
     */
    public function getRouting() : HttpRoutingInterface {
        return $this->routing;
    }
}