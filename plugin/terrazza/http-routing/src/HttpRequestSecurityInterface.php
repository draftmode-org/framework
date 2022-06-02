<?php
namespace Terrazza\Http\Routing;

interface HttpRequestSecurityInterface {
    /**
     * @return HttpRoutingInterface
     */
    public function getRouting() : HttpRoutingInterface;
}