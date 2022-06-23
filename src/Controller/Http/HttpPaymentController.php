<?php
namespace Terrazza\Framework\Controller\Http;

use Terrazza\Http\Request\HttpRequestInterface;
use Terrazza\Http\Response\HttpResponse;
use Terrazza\Http\Response\HttpResponseInterface;

class HttpPaymentController  {
    function getView(HttpRequestInterface $request): HttpResponseInterface {
        //throw new \InvalidArgumentException("invalid");
        $contentType = $request->getContentType();
        return new HttpResponse(200, [], "myContent");
    }
}