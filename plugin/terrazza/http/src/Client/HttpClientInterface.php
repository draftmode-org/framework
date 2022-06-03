<?php
namespace Terrazza\Http\Client;
use Terrazza\Http\Request\HttpRequestInterface;
use Terrazza\Http\Response\HttpResponseInterface;

interface HttpClientInterface {
    /**
     * @param HttpRequestInterface $request
     * @return HttpResponseInterface
     */
    public function sendRequest(HttpRequestInterface $request): HttpResponseInterface;
}