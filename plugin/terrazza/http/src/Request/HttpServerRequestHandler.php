<?php
namespace Terrazza\Http\Request;
use Terrazza\Http\Response\HttpResponseInterface;

class HttpServerRequestHandler implements HttpServerRequestHandlerInterface {
    /**
     * @inheritDoc
     */
    public function handle(HttpRequestInterface $request, HttpRequestHandlerInterface $requestHandler): HttpResponseInterface {
        return $requestHandler->handle($request);
    }
}