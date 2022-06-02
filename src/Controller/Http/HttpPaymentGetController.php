<?php
namespace Terrazza\Framework\Controller\Http;

use Terrazza\Http\Request\HttpRequestHandlerInterface;
use Terrazza\Http\Request\HttpRequestInterface;
use Terrazza\Http\Response\HttpResponse;
use Terrazza\Http\Response\HttpResponseInterface;

class HttpPaymentGetController implements HttpRequestHandlerInterface {

    function handle(HttpRequestInterface $request): HttpResponseInterface {
        var_dump(__NAMESPACE__);
        return new HttpResponse();
    }
}