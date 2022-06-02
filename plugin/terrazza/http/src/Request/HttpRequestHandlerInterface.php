<?php
namespace Terrazza\Http\Request;
use Terrazza\Http\Response\HttpResponseInterface;

interface HttpRequestHandlerInterface {
    /**
     * @param HttpRequestInterface $request
     * @return HttpResponseInterface
     */
    function handle(HttpRequestInterface $request) : HttpResponseInterface;
}