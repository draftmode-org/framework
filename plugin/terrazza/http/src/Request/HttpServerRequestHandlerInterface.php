<?php
namespace Terrazza\Http\Request;

use Terrazza\Http\Response\HttpResponseInterface;

interface HttpServerRequestHandlerInterface extends HttpRequestMiddlewareHandlerInterface {
    /**
     * @param HttpServerRequestInterface $request
     * @param HttpRequestHandlerInterface $requestHandler
     * @return HttpResponseInterface
     */
    public function handle(HttpRequestInterface $request, HttpRequestHandlerInterface $requestHandler): HttpResponseInterface;
}