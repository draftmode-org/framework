<?php
namespace Terrazza\Http\Message;
use Terrazza\Http\Request\HttpServerRequestInterface;
use Terrazza\Http\Response\HttpResponseInterface;

interface HttpMessageAdapterInterface {
    /**
     * @return HttpServerRequestInterface
     */
    public function getServerRequestFromGlobals(): HttpServerRequestInterface;

    /**
     * @param HttpResponseInterface $response
     */
    public function emitResponse(HttpResponseInterface $response): void;
}