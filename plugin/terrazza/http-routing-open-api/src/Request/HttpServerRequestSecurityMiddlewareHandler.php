<?php
namespace Terrazza\Http\Routing\OpenApi\Request;

use Terrazza\Http\Request\HttpRequestHandlerInterface;
use Terrazza\Http\Request\HttpRequestInterface;
use Terrazza\Http\Request\HttpServerRequestHandlerInterface;
use Terrazza\Http\Response\HttpResponseInterface;
use Terrazza\Http\Routing\HttpRequestSecurityInterface;

class HttpServerRequestSecurityMiddlewareHandler implements HttpServerRequestHandlerInterface {
    private HttpRequestSecurityInterface $security;
    public function __construct(HttpRequestSecurityInterface $security) {
        $this->security 						    = $security;
    }

    /**
     * @param HttpRequestInterface $request
     * @param HttpRequestHandlerInterface $requestHandler
     * @return HttpResponseInterface
     */
    public function handle(HttpRequestInterface $request, HttpRequestHandlerInterface $requestHandler): HttpResponseInterface {
        $route                                      = $this->security->getRouting()->getRoute($request->getUri(), $request->getMethod());
        if ($route) {
            var_dump(__METHOD__.":before");
        }
        $result 								    = $requestHandler->handle($request);
        if ($route) {
            var_dump(__METHOD__.":after");
        }
        return $result;
    }
}