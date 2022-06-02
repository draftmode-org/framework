<?php
namespace Terrazza\Http\Routing;
use Terrazza\Http\Request\HttpRequestInterface;

interface HttpRequestValidatorInterface {
    /**
     * @return HttpRoutingInterface
     */
    public function getRouting() : HttpRoutingInterface;

    /**
     * @param HttpRoute $route
     * @param HttpRequestInterface $request
     */
    public function validateParams(HttpRoute $route, HttpRequestInterface $request) : void;
}