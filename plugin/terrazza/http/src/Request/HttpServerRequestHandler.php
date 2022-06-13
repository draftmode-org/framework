<?php
namespace Terrazza\Http\Request;
use Terrazza\Http\Response\HttpResponseInterface;

class HttpServerRequestHandler implements HttpServerRequestHandlerInterface {
    /**
     * @param HttpRequestInterface $request
     * @param HttpRequestHandlerInterface $requestHandler
     * @return HttpResponseInterface
     */
    public function handle(HttpRequestInterface $request, HttpRequestHandlerInterface $requestHandler): HttpResponseInterface {
        return $requestHandler->handle($request);
    }
}